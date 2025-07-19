<html>
  <head>
    <meta charset="UTF-8"/>
    <title>Факти за България</title>
  </head>
  <body>
    <div align="center">

      <h2>M6 Практика:</h2>
      <h1>Факти за България</h1>
      <img src="bulgaria-map.png" />
      <table>
        <tr>
          <td>Площ</td>
          <td></td>
          <td>110 993.6 кв.км.</td>
        </tr>
        <tr>
          <td>Население</td>
          <td></td>
          <td>6 838 937</td>
        </tr>
        <tr>
          <td>Столица</td>
          <td></td>
          <td>София</td>
        </tr>
      </table>
      <br />
      <h1>Големи градове</h1>
      <table>
 
<h2> list: </h2>

<?php
   $show = isset($_GET['show']) ? $_GET['show'] : '1';

   if ($show === '1') {
       echo '<a href="?show=0">Скрий градовете</a>';
       echo '<table>';

       require_once ('config.php');
       try {
          $connection = new PDO("mysql:host={$host};dbname={$database};charset=utf8", $user, $password);
          $query = $connection->query("SELECT city_name, population FROM cities ORDER BY population DESC");
          $cities = $query->fetchAll();

          if (empty($cities)) {
             echo "<tr><td>Няма данни.</td></tr>\n";
          } else {
             foreach ($cities as $city) {
                echo "<tr><td>{$city['city_name']}</td><td align=\"right\">{$city['population']}</td></tr>\n";
             }
          }
       } catch (PDOException $e) {
          echo "<tr><td><div align='center'>";
          echo "Няма връзка към базата. Опитайте отново. <a href=\"#\" onclick=\"document.getElementById('error').style = 'display: block;';\">Детайли</a><br/>";
          echo "<span id='error' style='display: none;'><small><i>".$e->getMessage()." <a href=\"#\" onclick=\"document.getElementById('error').style = 'display: none;';\">Скрий</a></i></small></span>";
          echo "</div></td></tr>\n";
       }

       echo '</table>';
   } else {
       echo '<a href="?show=1">Покажи градовете</a>';
   }
?>

      
    </div>
  </body>
</html>


