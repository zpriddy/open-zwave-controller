<!DOCTYPE html>
<html>
    <head>
        <title>Web Client</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />
        <script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js"></script>
        <script>

            function purgeArray(ar)
            {
                var obj = {};
                var temp = [];
                for(var i=0;i<ar.length;i++)
                {
                    obj[ar[i]] = ar[i];
                }
                for (var item in obj)
                {
                    temp.push(obj[item]);
                }
                return temp;
            }

            function get_rooms(data){
                var devices = data.split("#");
                var device;
                var strHtml = '';

                //get rooms
                var count=0;
                var tmp = [];
                for (device in devices)
                {
                    deviceDetails = devices[device].split("~");
                    if(deviceDetails[2] != ''){
                        tmp[count] = deviceDetails[2];
                    }
                    count++;
                }
                rooms = purgeArray(tmp);;

                //get devices for each room

                for(room in rooms){
                    if(rooms[room]){
                        strHtml += '<div data-role="collapsible" data-content-theme="c">';
                        strHtml += '<h1>' + rooms[room] + '</h1><p>';

                        for (device in devices)
                        {
                            deviceDetails = devices[device].split("~");
                            if (deviceDetails[2] == rooms[room]){
                                strHtml += '<div data-role="collapsible" data-content-theme="c">';
                                strHtml += '<h3>' + deviceDetails[0] + '</h3>';
                                strHtml2="";

                                if (deviceDetails[3]=="Binary Switch" || deviceDetails[3]=="Binary Power Switch"){
                                    strHtml2 += '<form>    <table>        <tr>            <td>                <img width="48" src="../controlpanel/images/lightbulb.png" alt=""/>                <input type="hidden" name="value" id="' + 'level' + deviceDetails[1] + '" value="0"/>                <input type="hidden" name="type" id="' + 'type' + deviceDetails[1] + '" value="' + deviceDetails[3] + '"/>            </td>            <td>                <div data-role="controlgroup" data-type="horizontal">                    <a href="#" class="switch" data-role="button" name="off" id="' + deviceDetails[1] + '" >Off</a>                    <a href="#" class="switch" data-role="button" name="on" id="' + deviceDetails[1] + '"  >On</a>                </div>            </td>        </tr>    </table></form>';
                                }
                                if (deviceDetails[3]== "Multilevel Switch" || deviceDetails[3]=="Multilevel Power Switch"){
                                    strHtml2 += '<form>    <table>        <tr>            <td>                <img width="48" src="../controlpanel/images/lightbulb.png" alt=""/>                <input type="hidden" name="value" id="' + 'level' + deviceDetails[1] + '" value="0"/>                <input type="hidden" name="type" id="' + 'type' + deviceDetails[1] + '" value="' + deviceDetails[3] + '"/>            </td>            <td>                <div data-role="controlgroup" data-type="horizontal">                    <a href="#" class="switch" data-role="button" name="off" id="' + deviceDetails[1] + '" >&nbsp;Off&nbsp;</a>                    <a href="#" class="switch" data-role="button" name="on" id="' + deviceDetails[1] + '"  >&nbsp;On&nbsp;</a>                </div>                <div data-role="controlgroup" data-type="horizontal">                    <a href="#" class="switch" data-role="button" name="down" id="' + deviceDetails[1] + '">Dim</a>                    <a href="#" class="switch" data-role="button" name="up" id="' + deviceDetails[1] + '" >Bright</a>                </div>            </td>        </tr>    </table></form> ';
                                }
                                strHtml2 += '</p></div>';
                                strHtml += strHtml2;
                            }
                        }

                    }
                    strHtml += '</div>';
                }
                $("#roomlist").append(strHtml).trigger('create');
            }

            $(function() {

                $.get("../controlpanel/server.php", { command: 'devices'},
                function(data) {
                    get_rooms(data);
                    $('.switch').click(function() {
                        var level = parseInt($('#level'+$(this).attr('id')).val());
                        switch($(this).attr('name')){
                            case "up":
                                if (level >= 0 && level < 100){
                                    level += 10;
                                }
                                break;

                            case "down":
                                if (level >= 10 && level <= 100){
                                    level -= 10;
                                }
                                break;

                            case "on":
                                level = 100;
                                break;

                            case "off":
                                level = 0;
                                break;
                        }

                        $('#level'+$(this).attr('id')).val(level);
                        node = $(this).attr("id");
                        type = $("#type"+node).val();
                        $.get("../controlpanel/server.php", { command: 'control', node: node, level: level, type: type },
                        function(data) {
                            $("#response").html(data);
                        });

                    });
                });



            });


        </script>
    </head>
    <body>
        <div data-role="page" id="main">
            <div data-role="header" data-position="inline">
                <h1>Home Control</h1>
                <!--<a href="settings.html" data-icon="gear" class="ui-btn-right">Options</a>-->
            </div>
            <div class="ui-bar ui-bar-b">
                <div id="response">&nbsp;</div>
            </div>
            <div data-role="content">
                <div id="roomlist"></div>
            </div>
        </div>
    </body>
</html>