<?php
require 'database.php';
include 'html_header.php';
?>


        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.12.0/jquery.validate.min.js"></script>
  

        <style type="text/css">
            label.error {
                color: red;
                font-size: 16px;
                font-weight: normal;
                line-height: 1.4;
            }
        </style>
        <script type="text/javascript">//<![CDATA[ 
            $(window).load(function() {
                $("#frmLogin").validate({
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
            });//]]>  
        </script>
    </head>
    <body class="ui-mobile-viewport ui-overlay-a">
        <div data-role="page" id="login">

            <div data-role="header">
                <h1>RNLI Crewlist</h1>
            </div>

            <div data-role="content">

                <form id="frmLogin" class="validate" method="post" action="<?php print(ROOT_URL) ?>">
                    <div data-role="fieldcontain">
                        <label for="name">Name: </label>
                        <select name="name" id="name" class="required">
                            <option value="">Please select</option>
                            <?php displayAllCrewSelectOptions($db) ?>
                        </select>
                    </div>

                    <div data-role="fieldcontain">
                        <label for="password">Password: </label>
                        <input type="password" id="password" name="password"
                               class="required" />
                    </div>

                    <div class="ui-body">
                        <button class="btnLogin" type="submit" 
                                data-theme="a">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<?php

function displayAllCrewSelectOptions($db) {
    $stmt = $db->query('SELECT name FROM crew ORDER BY name');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        print('<option value="' . $row['name'] . '">');
        print($row['name']);
        print('</option>');
    }
}
