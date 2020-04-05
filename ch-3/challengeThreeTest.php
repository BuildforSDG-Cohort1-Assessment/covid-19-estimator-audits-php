<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../test-utils.php';
require_once __DIR__ . '/../../src/estimator.php';

class challengeOneTest extends TestCase
{
  public function estimatorDataProvider()
  {
    return [
      "days - challenge 3" => ["days", "ch-3"],
      "weeks - challenge 3" => ["weeks", "ch-3"],
      "months - challenge 3" => ["months", "ch-3"]
    ];
  }

  /**
   * @test
   * @testdox
   * @dataProvider estimatorDataProvider
   */
  public function estimate_current_and_projected_infections($duration, $challenge)
  {
    $response_object = mockEstimatorFor($duration);
    $this->assertIsObject($response_object, "Something went wrong with testing data");

    $estimation = convertEstimatesToArray($response_object);
    $data = $estimation["data"];
    $estimate = $estimation["estimate"];

    $result = covid19ImpactEstimator($data);

    foreach ($GLOBALS['fields'][$challenge] as $key) {
      $this->assertArrayHasKey($key, $result['impact']);
      $this->assertArrayHasKey($key, $result['severeImpact']);
    }

    $this->assertIsArray($result);
    $this->assertNotEmpty($result);
    $this->assertArrayHasKey('impact', $result);
    $this->assertArrayHasKey('severeImpact', $result);
    $this->assertIsArray($result['impact']);
    $this->assertIsArray($result['severeImpact']);

    $values = valueOnFields($result, $estimate, $challenge);
    foreach ($values as $estimation) {
      $produced = $estimation[0];
      $expected = $estimation[1];
      $this->assertEquals($produced, $expected);
    }
  }
}
