<?php

    /**
     * Deze functie laat alle banen in het systeem zien	 *
     */
    function displayAllJobs() {
        global $mysqli;

        $sql = "SELECT * FROM `Jobs` ORDER BY `JobTitle`";
        $result = $mysqli->query($sql);



    }


    /**
     * Deze functie laat het banen toevoeg formulier zien	 *
     */
    function displayAddJob() {


    }

    /**
     * Deze functie laat het banen bewerkformulier zien.
     * Dit formulier is automatisch gevuld met de gegevens die bij het meegegeven ID horen	 *
     */

    function displayEditJob() {
        global $mysqli;

        $sql = sprintf( "SELECT * FROM `Jobs` WHERE `JobID` = %d",
                        $mysqli->escape_string($_GET['id']) );

        $result = $mysqli->query($sql);

        if($row = $result->fetch_assoc()) {

            $row = escapeArray($row); // alle slashes weghalen

            echo"<h1>Baan bewerken</h1>";

            echo" <form method=\"post\" action=\"index.php?action=updatejob\">";
            echo" 	<table>";
            echo"		<tr>";
            echo"			<td>Titel:</td>";
            echo"			<td><input type=\"text\" name=\"JobTitle\" value=\"".$row['JobTitle']."\" /></td>";
            echo"		</tr>";
            echo"		<tr>";
            echo"			<td>Minimuloon:</td>";
            echo"			<td><input type=\"text\" name=\"MinSalary\" value=\"".$row['MinSalary']."\" /></td>";
            echo"		</tr>";
            echo"		<tr>";
            echo"			<td>Maximumloon:</td>";
            echo"			<td><input type=\"text\" name=\"Maxsalary\" value=\"".$row['MaxSalary']."\" /></td>";
            echo"		</tr>";
            echo"		<tr>";
            echo"			<td></td>";
            echo"			<td><input type=\"submit\" value=\"Opslaan\" /></td>";
            echo"		</tr>";
            echo" 	</table>";

            echo" <input type=\"hidden\" name=\"JobID\" value=\"".$row['JobID']."\" />";

            echo" </form>";

        }
        else {
            die("Geen gegevens gevonden");
        }
    }

    /**
     * Deze functie voegt een nieuwe record toe aan de tabel Jobs	 *
     */
    function addJob() {
        global $mysqli;

        // Letop we maken gebruik van sprintf. Kijk op php.net voor meer info.
        // Binnen sprintf staat %s voor een string, %d voor een decimaal (integer) en %f voor een float

        $sql = sprintf("INSERT INTO `Jobs` (`JobTitle`, `MinSalary`, `MaxSalary`) VALUES  ('%s', '%f', '%f')",
                        $mysqli->escape_string($_POST['JobTitle']),
                        $mysqli->escape_string($_POST['MinSalary']),
                        $mysqli->escape_string($_POST['MaxSalary']) );

        $mysqli->query($sql);

        header("location: index.php?action=jobs"); // terugkeren naar jobs
        exit();
    }

    /**
     * Deze functie werkt de record met ID $_POST['JobID'] bij	 *
     */

    function updateJob() {
        global $mysqli;
        $sql = sprintf("UPDATE `Jobs` SET
                        `JobTitle` = '%s',
                        `MinSalary` = '%s',
                        `MaxSalary` = '%s'
                        WHERE `JobID` = %d",
                        $mysqli->escape_string($_POST['JobTitle']),
                        $mysqli->escape_string($_POST['MinSalary']),
                        $mysqli->escape_string($_POST['MaxSalary']),
                        $mysqli->escape_string($_POST['JobID']) );

        $mysqli->query($sql);

        header("location: index.php?action=jobs"); // terugkeren naar jobs
        exit();
    }

    /**
     * Deze functie verwijderd record met id $_GET['ID']  uit de tabel Jobs
     */
    function deleteJob() {
        global $mysqli;

        $sql = sprintf("DELETE FROM `Jobs` WHERE `JobID` = %d", $mysqli->escape_string($_GET['id']));
        $mysqli->query($sql);

        header("location: index.php?action=jobs"); // terugkeren naar jobs
        exit();
    }

?>
