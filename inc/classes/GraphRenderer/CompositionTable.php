<?php

/**
 *
 */
class GraphRenderer_CompositionTable extends GraphRenderer_CompositionPie {

  public function getTitle() {
    return ct("Your :currency balances");
  }

  public function getTitleArgs() {
    return array(
      ':currency' => get_currency_abbr($this->currency),
    );
  }

  public function getChartType() {
    return "vertical";
  }

  var $total;

  public function getCustomSubheading() {
    return $this->total;
  }

  /**
   * We need to transpose the returned data, both data and columns.
   */
  public function getData($days) {
    $original = parent::getData($days);

    $columns = array();

    $columns[] = array('type' => 'string', 'title' => ct("Total :currency"), 'args' => array(':currency' => get_currency_abbr($this->currency)), 'heading' => true);

    $data = array();
    $total = 0;
    foreach ($original['data'] as $key => $row) {
      foreach ($row as $i => $value) {
        $data[] = array(
          $original['columns'][$i]['title'],
          currency_format($this->currency, $value, 4),
        );
        $total += $value;
      }
    }

    // 'Total BTC' column
    $columns[] = array('type' => 'string', 'title' => currency_format($this->currency, $total, 4));

    // save for later
    $this->total = $total;

    return array(
      'key' => $original['key'],
      'columns' => $columns,
      'data' => $data,
      'last_updated' => $original['last_updated'],
    );

  }

}
