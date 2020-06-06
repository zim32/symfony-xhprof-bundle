<?php

namespace Zim\XhProfBundle\DataCollector;

use Zim\XhProfBundle\ZimXhProfBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class XhProfDataCollector extends DataCollector
{

    /**
     * @inheritdoc
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            'stats'    => [],
            'env_data' => $this->collectEnvData()
        ];

        if (false === ZimXhProfBundle::$needToCollect) {
            return;
        }

        $data = tideways_xhprof_disable();

        uasort($data, function(array $a, array $b) {
            return $b['wt'] - $a['wt'];
        });

        $this->data['stats'] = $data;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'zim.xhprof_data_collector';
    }

    /**
     * @inheritdoc
     */
    public function reset()
    {

    }

    public function getXhProfData()
    {
        $stats = $this->data['stats'];

        foreach ($stats as $execution => &$data) {
            $tmp = explode('==>', $execution);

            $data['parent'] = $tmp[0];
            $data['child']  = isset($tmp[1]) ? $tmp[1] : '';

            $data['parent_trimmed'] = $this->trimString($data['parent']);
            $data['child_trimmed']  = $this->trimString($data['child']);
        }

        if (isset($_GET['sortBy'])) {
            uasort($stats, function(array $a, array $b) {
                if ($_GET['sortOrder'] === 'asc') {
                    return $a[$_GET['sortBy']] - $b[$_GET['sortBy']];
                } else {
                    return $b[$_GET['sortBy']] - $a[$_GET['sortBy']];
                }
            });
        }

        return $stats;
    }

    public function getEnvData()
    {
        return $this->data['env_data'];
    }

    protected function collectEnvData()
    {
        return [
            'ZIM_XHPROF_ENABLE'    => array_key_exists('ZIM_XHPROF_ENABLE', $_ENV) ? $_ENV['ZIM_XHPROF_ENABLE']  : 'NOT SET',
            'ZIM_XHPROF_CONDITION' => array_key_exists('ZIM_XHPROF_CONDITION', $_ENV) ? $_ENV['ZIM_XHPROF_CONDITION']  : 'NOT SET',
            'extension_loaded'     => extension_loaded('tideways_xhprof') ? 'YES' : 'NO',
        ];
    }

    public function trimString(string $value)
    {
        if (strlen($value) <= 50) {
            return $value;
        }

        return '...' . substr($value, strlen($value) - 50, strlen($value));
    }
}