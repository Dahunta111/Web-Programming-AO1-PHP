
 	<!DOCTYPE HTML>
 	<html>
 	<head>
 	<title>Design</title>
 	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
 	</head>
    <body>
        
        
<?php
        
$dbName ='mysql:host=localhost; dbname=database';
$user= 'theUser';
$pass = 'aPassword';

    try{
        $conn = new PDO(
        $dbName, 
        $user, 
        $pass);
        } 
        catch(PDOException $exception)
        {
               echo "Oh no, there was a problem" . $exception->getMessage();
        }

        $query = "SELECT * FROM anime";
        $results = $conn->query($query);    
    
?>
        
        
         <header class ="container">
        <div class="row">
        <div class="col-md-6 col-sm-6" id="mainTitle">
        <h1>Database Design and Implementation</h1>
        </div>
        <div class="col-md-6 col-sm-6" id="navigation">
        <ul>    
            <li><a href ="index.php">Database Search</a></li>
            <li><a href="design.php">Database Design</a></li>
        </ul>
        </div>
            </div>
        </header>

  <div class="container backing">
             <article class="row"> 
                 <section class="col-md-12 col-sm-12">
                     <section class="info-section">
                         <h1>Scenario Description</h1>
                           <p>The scenario is to allow any users to search an anime and then find more information about it or a particular episode.</p>
                            <p>The website allows users to browse anime series by the name and genre. There is also a show all button. Once a anime has been selected the user will be able to go to a details page. This page will have the season information, episode information and related anime. The anime searches through all the genres that the anime you selected has and will display the first three.</p>
                         
                       
                         </section>
        <img src ="image/class_diagram.svg" alt="class Diagram"/>
        <img src ="image/physical_diagram.svg" alt ="physical Diagram"/>
    <h1>Data Dump</h1>
<?php   

    echo "<h2>Anime</h2>";
    echo "<table>";



    echo "<tr>
        <th> animeID</th> 
        <th> Name</th>     
        <th> description</th>
        <th> duration</th>
        <th> startDate</th>
        <th> endDate</th>
        </tr>";



    while ($anime = $results->fetch()) {

        echo "<tr>";

        echo "<td>"
            .$anime["animeID"]."</td> <td>"
            .$anime["name"]."</td> <td>"
            .$anime["description"]."</td> <td>"
            .$anime["duration"]."</td> <td>"
            .$anime["startDate"]."</td> <td>"
            .$anime["endDate"].
            "</td>";
    }
                     
    echo "</tr>";
    echo "</table>";


/*_____________________*/

    echo "<h2>anime_genre</h2>";
    echo "<table>"; 

    echo "<tr>
            <th> animeID</th> 
            <th> genreID</th>
            </tr>";     


    $query = "SELECT * FROM anime_genre";
    $results = $conn->query($query); 


    while ($genre_anime = $results->fetch()) {

        echo "<tr>";

        echo "<td>"
        .$genre_anime["animeID"]."</td> <td>"
        .$genre_anime["genreID"]."</td>";

    }
        echo "</tr>";
        echo "</table>";

/*_____________________*/

    echo "<h2>Genre</h2>";
    echo "<table>"; 

    echo "<tr>
        <th> genreID</th> 
        <th> genre</th>
        <th> description</th>
        </tr>";     

    $query = "SELECT * FROM genre";
    $results = $conn->query($query);
    while ($genre = $results->fetch()) {

        echo "<tr>";

        echo "<td>"
            .$genre["genreID"]."</td> <td>"
            .$genre["name"]."</td> <td>"
            .$genre["description"]."</td>";



    }
                     
    echo "</tr>";
    echo "</table>";


/*_____________________*/

    echo "<h2>Arc</h2>";
    echo "<table>"; 

    echo "<tr><th> arcID</th> 
        <th> name</th>
        <th> description</th>
        <th> Season</th>
        <th>animeID</th></tr>";     

    $query = "SELECT * FROM arc";
    $results = $conn->query($query);
                     
    while ($arc = $results->fetch()) {

        echo "<tr>";

        echo "<td>"
            .$arc["arcID"]."</td> <td>"
            .$arc["name"]."</td> <td>"
            .$arc["description"]."</td><td>"
            .$arc["season"]."</td><td>"
            .$arc["animeID"]."</td>";



    }
    echo "</tr>";

    echo "</table>";

    /*_____________________*/

    echo "<h2>Episodes</h2>";
    echo "<table>"; 

    echo "<tr><th> arcID</th> 
            <th> name</th>
            <th> description</th>
            <th>episode</th>
            <th>animeID</th></tr>";     

    $query = "SELECT * FROM episode";
    $results = $conn->query($query);
                     
    while ($episode = $results->fetch()) {

        echo "<tr>";

        echo "<td>"
            .$episode["episodeID"]."</td> <td>"
            .$episode["name"]."</td> <td>"
            .$episode["description"]."</td><td>"
            .$episode["episode"]."</td><td>"
            .$episode["arcID"]."</td>";

    }
                     
    echo "</tr>";
    echo "</table>";

    $conn=NULL;

?>

      
                </section>
            </article>
        </div>
    </body>
 </html>