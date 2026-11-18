<?php
/**
 * FlowIT - Cliente Zabbix API JSON-RPC 2.0 (Compatível Zabbix 7.x Bearer Token)
 */

class FlowitZabbixClient {
    private $apiUrl;
    private $authToken;

    public function __construct($apiUrl = null, $authToken = null) {
        global $DB;
        if (!$apiUrl) {
            if ($DB) {
                try {
                    $iterator = $DB->request([
                        'FROM' => 'glpi_plugin_flowit_monitoring_servers',
                        'WHERE' => ['is_active' => 1],
                        'ORDER' => 'is_default DESC',
                        'LIMIT' => 1
                    ]);
                    foreach ($iterator as $server) {
                        $this->apiUrl = $server['api_url'];
                        $this->authToken = $server['auth_token'];
                        break;
                    }
                } catch (\Throwable $e) {}
            }
        } else {
            $this->apiUrl = $apiUrl;
            $this->authToken = $authToken;
        }

        if (empty($this->apiUrl)) {
            $this->apiUrl = 'http://192.168.77.208/zabbix/api_jsonrpc.php';
        }
        if (empty($this->authToken)) {
            $this->authToken = '3714a4efa84bfd8e527b378cbdc98cb8b2a4d27cb49d32eb48f52cda132f71a6';
        }
    }

    public function call($method, $params = [], $auth = null) {
        $token = $auth !== null ? $auth : $this->authToken;
        $payload = [
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => $params,
            'id'      => mt_rand(1, 99999)
        ];

        $headers = [
            'Content-Type: application/json-rpc',
            'User-Agent: FlowIT-Zabbix/2.0'
        ];

        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $jsonPayload = json_encode($payload);

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['error' => ['code' => -1, 'message' => 'cURL Error: ' . $err]];
        }

        $decoded = json_decode($response, true);
        if ($decoded === null) {
            return ['error' => ['code' => -2, 'message' => 'Resposta JSON inválida da API Zabbix: ' . substr($response, 0, 200)]];
        }

        return $decoded;
    }

    public function getVersion() {
        $res = $this->call('apiinfo.version', []);
        return $res['result'] ?? null;
    }

    public static function checkPort($ip, $port = 10050, $timeout = 1.5, $protocol = 'tcp') {
        $start = microtime(true);
        if ($protocol === 'udp') {
            $fp = @fsockopen("udp://$ip", $port, $errno, $errstr, $timeout);
            if ($fp) {
                fwrite($fp, "\x00");
                stream_set_timeout($fp, (int)$timeout);
                $res = fread($fp, 1);
                fclose($fp);
                $latency = round((microtime(true) - $start) * 1000, 2);
                return ['success' => true, 'latency_ms' => $latency, 'message' => 'Porta UDP acessível'];
            }
            return ['success' => false, 'message' => 'UDP unreachable / timeout'];
        }

        $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        $latency = round((microtime(true) - $start) * 1000, 2);
        if ($fp) {
            fclose($fp);
            return ['success' => true, 'latency_ms' => $latency, 'message' => "Porta $port/TCP aberta ($latency ms)"];
        }
        return ['success' => false, 'message' => "Porta $port/TCP não respondeu ($errstr)"];
    }

    public static function pingIcmp($ip, $count = 2) {
        $start = microtime(true);
        $cmd = "ping -c $count -W 2 " . escapeshellarg($ip) . " 2>&1";
        $out = [];
        $ret = 0;
        @exec($cmd, $out, $ret);
        $latency = round((microtime(true) - $start) * 1000, 2);
        $outputText = implode("\n", $out);

        if ($ret === 0) {
            preg_match('/min\/avg\/max[^\=]*=\s*([^\/]+)\/([^\/]+)\//i', $outputText, $matches);
            $avg = isset($matches[2]) ? floatval($matches[2]) : $latency;
            return ['success' => true, 'latency_ms' => $avg, 'message' => "Ping respondendo ($avg ms)"];
        }
        return ['success' => false, 'latency_ms' => null, 'message' => 'Host inalcançável via ICMP (100% perda)'];
    }
}
