<?php

namespace Mgrt\Wordpress\Manager;

use Mgrt\Model\MailingList;
use Mgrt\Model\WebhookCallInput;
use Mgrt\Wordpress\Bootstrap;
use Mgrt\Wordpress\Sync\ImportSyncProcessor;
use Mgrt\Wordpress\Sync\ExportSyncProcessor;

class SyncManager
{
    const FORCED_EXPORT_SYNC_LENGTH = 8;
    const FORCED_IMPORT_SYNC_LENGTH = 15;

    private $max_exec_time;
    private $sync_start_time;

    private $bootstrap;
    private $export_executor = null;
    private $import_executor = null;

    private $import_sync = false;
    private $export_sync = false;

    public static $is_syncing = false;
    public static $notify_user_registration = true;
    public static $hook_success = false;

    function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;

        $this->max_exec_time = ini_get('max_execution_time') * 0.80;

        $this->export_executor = new ExportSyncProcessor($this->bootstrap);
        $this->import_executor = new ImportSyncProcessor($this->bootstrap);

        if (!$this->bootstrap->getDataManager()->getOption('enable_sync')) {
            return;
        }

        switch ($this->bootstrap->getDataManager()->getOption('sync_direction')) {
            case 'up':
                $this->registerExportExecutor();
            break;
            case 'both':
                $this->registerExportExecutor();
            case 'down':
                $this->registerImportExecutor();
        }

        /**
         * Register ajax call for sync
         */
        add_action('wp_ajax_mgrt_force_sync', function() {
            if (!$this->bootstrap->getDataManager()->getOption('enable_sync')) {
                exit(json_encode(array('continue' => false, 'msg' => 'Sync disabled')));
            }

            $data = $this->doForceSync($_POST);
            die (json_encode($data));
        });
    }

    public static function getFields()
    {
        return array(
            'first_name'    => __('First Name'),
            'last_name'     => __('Last Name'),
            'nickname'      => __('Nickname'),
            'user_url'      => __('Website'),
            'description'   => __('Biographical Info')
        );
    }

    public function getListenedEvents()
    {
        return array(
            'contact.create',
            'contact.edit',
            'contact.unsubscribe',
            'contact.delete'
        );
    }

    public function getListenedSources()
    {
        return array(
            'app',
            'subscriber',
        );
    }

    /**
     * Start a force sync
     * @param array $recall last known state
     * @return array
     */
    public function doForceSync($recall = array())
    {
        $this->sync_start_time = time();
        $timeStart = microtime(true);

        self::$is_syncing = true;
        $sync_direction = $this->bootstrap->getDataManager()->getOption('sync_direction');

        self::$notify_user_registration = isset($recall['notify']) && $recall['notify'];

        // removed action (used only for wordpress)
        if (isset($recall['action'])) {
            unset($recall['action']);
        }

        // unset priority if direction ! both
        if ($sync_direction != 'both' || empty($recall['priority'])) {
            $recall['priority'] = 0;
        }

        if (!isset($recall['priority'])) {
            $recall['priority'] = 0;
        }

        if (!isset($recall['sequence'])) {
            $recall['sequence'] = 0;
        }

        // sanitize mode & priority
        if (empty($recall['mode'])) {
            if ($sync_direction == 'both') {
                if ($recall['priority'] == 0) {
                    $recall['mode'] = 'up';
                } else {
                    $recall['priority'] = 1;
                    $recall['mode'] = 'down';
                }

            } else {
                $recall['mode'] = $sync_direction;
            }
        }

        /**
         * sanitize page
         */
        if (empty($recall['page'])) {
            $recall['page'] = 1;
        } else {
            $recall['page'] = (int) $recall['page'];
        }

        $last_recall = $recall; // logs

        if ($recall['mode'] == 'up') {
            $result = $this->doExportSync($recall['page']);

        } elseif ($recall['mode'] == 'down') {
            $lists = $this->bootstrap->getDataManager()->getOption('lists');
            if (empty($lists)) {
                $lists = array(-1);
            }
            if (!isset($recall['list'])) {
                $recall['list'] = $lists[0];
                $recall['listi'] = 0;
            }

            $result = $this->doImportSync($recall['page'], $recall['list']);
        }

        if (!$result['continue']) {
            if ($recall['mode'] == 'down' && $recall['listi'] < count($lists)-1) {
                $recall['mode'] = 'down';
                $recall['list'] = $lists[++$recall['listi']];
                $recall['page'] = 1;
                $result['continue'] = true;
            } else if ($sync_direction == 'both') {
                if ($recall['sequence'] == 0) {
                    $recall['sequence']++;
                    $recall['mode'] = $recall['priority'] == 0 ? 'down' : 'up';
                    $recall['page'] = 1;
                    $result['continue'] = true;
                }
            }
        } else {
            $recall['page']++;
        }

        $result['next_recall'] = $recall;
        $result['last_recall'] = $last_recall;
        $result['total_time'] = microtime(true) - $timeStart;

        self::$is_syncing = false;
        self::$notify_user_registration = true;

        return $result;
    }

    /**
     * do a forced export sync
     *
     * @param integer $page page index
     * @return array
     */
    private function doExportSync($page = 1)
    {
        // TIMING
        $time = microtime(true);
        // TIMING
        $result = array();

        $totalContacts = $this->bootstrap->getDataManager()->getTotalUsers();

        $user_query = get_users(array(
            'offset' => ($page-1) * self::FORCED_EXPORT_SYNC_LENGTH,
            'number' => self::FORCED_EXPORT_SYNC_LENGTH
        ));

        $result['results'] = array();
        $result['times'] = array();

        // TIMING
        $result['before_time'] = microtime(true) - $time;
        // TIMING

        $done = 0;
        foreach ($user_query as $user) {
            // TIMING
            $time = microtime(true);
            // TIMING

            $fields = array();
            foreach (self::getFields() as $key => $value) {
                $fields[$key] = $user->get($key);
            }
            $fields['url'] = $user->get('user_url');
            $fields['email'] = $user->get('user_email');

            $r = array(
                'email'  => $fields['email'],
                'status' => $this->export_executor->onUserEdit($user->get('ID'), $fields['email'], $fields)
            );
            // TIMING
            $time = microtime(true) - $time;
            // TIMING

            $r['time'] = $time;
            $result['times'][] = $time;
            $result['results'][] = $r;
            $done++;

            // break if timeout is near
            if (time() - $this->sync_start_time >= $this->max_exec_time) {
                break;
            }
        }
        // TIMING
        $time = microtime(true);
        // TIMING

        $result['count'] = $done;
        $result['done'] = ($page-1) * self::FORCED_EXPORT_SYNC_LENGTH + $done;
        $result['continue'] = ($result['done'] != $totalContacts);
        $result['total'] = $totalContacts;

        // TIMING
        $result['after_time'] = microtime(true) - $time;
        // TIMING

        return $result;
    }

    /**
     * do a forced import sync
     *
     * @param integer $page page index
     * @param integer $list_id list id
     * @return array
     */
    private function doImportSync($page = 1, $list_id)
    {
        // TIMING
        $time = microtime(true);
        // TIMING

        $list_id = (int) $list_id;
        $result = array(
            'page' => $page,
            'listId' => $list_id
        );
        $opts = array();

        if ($list_id != -1) {
            $ml = new MailingList();
            $ml->setId(intval($list_id));
            $opts[] = $ml;
        }
        $opts[] = array(
            'page' => intval($page),
            'limit' => self::FORCED_IMPORT_SYNC_LENGTH,
            'status' => 'active'
        );

        $contacts = $this->bootstrap->getDataManager()->makeApiCall(
            $list_id != -1 ? 'getMailingListContacts' : 'getContacts',
            $opts
        );

        $result['results'] = array();
        $result['times'] = array();

        // TIMING
        $result['before_time'] = microtime(true) - $time;
        // TIMING

        $done = 0;
        foreach ($contacts as $contact) {
            // TIMING
            $time = microtime(true);
            // TIMING

            $r = array(
                'email' => $contact->getEmail(),
                'status' => $this->import_executor->onCreate($contact),
            );

            // TIMING
            $time = microtime(true) - $time;
            // TIMING

            $r['time'] = $time;
            $result['times'][] = $time;
            $result['results'][] = $r;
            $done++;

            // break if timeout is near
            if (time() - $this->sync_start_time >= $this->max_exec_time) {
                break;
            }
        }
        $time = microtime(true);

        $result['count'] = $done;
        $result['done'] = ($page-1) * self::FORCED_IMPORT_SYNC_LENGTH + $done;
        $result['continue'] = $result['done'] < $contacts->getTotal();
        $result['total'] = $contacts->getTotal();

        // TIMING
        $result['after_time'] = microtime(true) - $time;
        // TIMING
        return $result;
    }

    /**
     * Register hooks for exporter
     */
    private function registerExportExecutor()
    {
        $this->export_sync = true;
        add_action('user_register', array($this->export_executor, 'onUserRegister'));
        add_action('delete_user', array($this->export_executor, 'onUserDelete'));
        add_action('profile_update', array($this->export_executor, 'onUserEdit'), 10, 2);
    }

    /**
     * Register hooks for importer
     */
    private function registerImportExecutor()
    {
        $this->import_sync = true;
        if (isset($_GET['webhook']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $call = new WebhookCallInput();
            $call->setSecretKey($this->bootstrap->getDataManager()->getOption('webhook_secret_key', MGRT__OPTION_KEY.'-webhook'));
            $call->fromInputs();

            if ($call === false) {
                exit('error?');
            }

            if ($call->getValid()) {
                if ($this->import_executor->onWebhook($call)) {
                    echo 'ok';
                } else {
                    echo 'failure';
                }

            } else {
                echo 'BAD_SIGNATURE';
            }

            exit;
        }
    }


    public function getExportExecutor()
    {
        return $this->export_executor;
    }


    public function shouldDoImportSync()
    {
        return $this->import_sync;
    }
    public function shouldDoExportSync()
    {
        return $this->export_sync;
    }
}
