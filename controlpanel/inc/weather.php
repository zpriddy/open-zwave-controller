<div id="weather">	
    <table>
        <tr>
            <td colspan="2">WEATHER</td>
        </tr>
        <?php
        $headings = array(
            "temperature" => "Temp:",
            "wind" => "Wind:",
            "humidity" => "Humidity:");


        $xmlstr = implode(file("http://www.maltaweather.net/naxxar/wx7.xml"));

        $xml = new SimpleXMLElement($xmlstr);
        //print_r($xml);
        foreach ($xml as $weather) {
            foreach ($weather as $key => $value) {
                if (key_exists($key, $headings)) {
                    echo "<tr>";
                    echo "<th class=\"thstyle\">" . $headings[$key] . '</th><td>' . $value . "</td>";
                    echo "</tr>";
                }
            }
        }
        ?>
    </table>
</div>
