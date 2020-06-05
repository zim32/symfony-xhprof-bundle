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
        if (false === ZimXhProfBundle::$needToCollect) {
            return;
        }

        $data = tideways_xhprof_disable();

        uasort($data, function(array $a, array $b) {
            return $b['wt'] - $a['wt'];
        });

        $this->data = $data;
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
        foreach ($this->data as $execution => &$data) {
            $tmp = explode('==>', $execution);

            $data['parent'] = $tmp[0];
            $data['child']  = isset($tmp[1]) ? $tmp[1] : '';

            $data['parent_trimmed'] = $this->trimString($data['parent']);
            $data['child_trimmed']  = $this->trimString($data['child']);
        }

        if (isset($_GET['sortBy'])) {
            uasort($this->data, function(array $a, array $b) {
                if ($_GET['sortOrder'] === 'asc') {
                    return $a[$_GET['sortBy']] - $b[$_GET['sortBy']];
                } else {
                    return $b[$_GET['sortBy']] - $a[$_GET['sortBy']];
                }
            });
        }

        return $this->data;
    }

    public function trimString(string $value)
    {
        if (strlen($value) <= 50) {
            return $value;
        }

        return '...' . substr($value, strlen($value) - 50, strlen($value));
    }
}