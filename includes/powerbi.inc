<?php
/**
 *
 */

use Firebase\JWT\JWT;

class Powerbi {

  protected $appTokens = array();
  protected $apiToken;
  protected $baseUrl;
  protected $embedTokens = array();
  protected $reports = array();
  protected $version = "0.1.0";
  protected $workspaceCollection;
  protected $workspaceId;

  function __construct(array $appTokens, $workspaceCollection, $workspaceId) {
    $this->appTokens = $appTokens;
    $this->workspaceCollection = $workspaceCollection;
    $this->workspaceId = $workspaceId;
    $this->baseUrl = "https://api.powerbi.com/beta/collections/$this->workspaceCollection/workspaces/$this->workspaceId/";
  }

  /**
   * Get a single report by id.
   *
   * @param $report_id
   * @return array
   */
  function getReport($report_id) {
    $this->getReports();
    if (!empty($this->reports)) {
      $report = array_filter($this->reports['value'], function($value) use ($report_id) {
        return $value['id'] === $report_id;
      });
      return reset($report);
    }
    return FALSE;
  }

  /**
   * List all reports in the current workspace.
   *
   * @param bool $refresh
   */
  function getReports($refresh = FALSE) {
    if (empty($this->reports) || $refresh) {
      $this->reports = $this->call("reports");
    }

    return isset($this->reports['value']) ? $this->reports['value'] : array();
  }

  /**
   * Create a token for use in api requests.
   */
  private function getApiToken() {
    if (is_null($this->apiToken)) {
      $key = reset($this->appTokens);
      $payload = array(
        "ver" => $this->version,
        "type" => "dev",
        "wcn" => $this->workspaceCollection,
        "wid" => $this->workspaceId,
        "iss" => "PowerBISDK",
        "aud" => "https://analysis.windows.net/powerbi/api",
        "exp" => time()+60*60,
        "nbf" => time()
      );
      $this->apiToken = JWT::encode($payload,$key);
    }
    return $this->apiToken;
  }

  /**
   * @param $report
   */
  function getEmbedToken($reportId) {
    if (!is_array($this->embedTokens)
        || !isset($this->embedTokens[$reportId])
        || $this->embedTokens[$reportId]['exp'] > time()) {
      $key = reset($this->appTokens);
      $expiration = time()+60*60;
      $payload = array(
        "ver" => $this->version,
        "type" => "embed",
        "wcn" => $this->workspaceCollection,
        "wid" => $this->workspaceId,
        'rid' => $reportId,
        "iss" => "PowerBISDK",
        "aud" => "https://analysis.windows.net/powerbi/api",
        "exp" => $expiration,
        "nbf" => time()
      );
      $this->embedTokens[$reportId] = array('token' => JWT::encode($payload, $key), 'exp' => $expiration);
    }

    return $this->embedTokens[$reportId]['token'];
  }

  private function call($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Authorization: AppToken " . $this->getApiToken()
    ));
    $response = curl_exec($ch);
    if(curl_error($ch)) {
      return false;
    }

    return json_decode($response, true);
  }
}
