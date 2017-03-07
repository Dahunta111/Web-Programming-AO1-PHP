
        <!DOCTYPE HTML>
        <html>
        <head>
        <title>Details</title>
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


        $animeID=$_GET['id'];
        $arcID=$_GET['arcID'];



         $animeInfo = $conn -> prepare("SELECT `anime`.`animeID`, `anime`.`name`, `anime`.`description`, `anime`.`duration`, `anime`.`startDate`, `anime`.`endDate`, `anime`.`image`
        FROM `anime` WHERE `anime`.`animeID` = :animeID");

        $animeInfo -> bindValue(':animeID', $animeID,  PDO::PARAM_INT);
        $animeInfo -> execute();
        
        //show basic info about the anime
        $displayAnimeInfo = $animeInfo -> fetch();

        ?>
            
        <header class ="container">
            <div class="col-md-4 col-sm-6" id="mainTitle">

    <?php  

      echo "<h1>".$displayAnimeInfo["name"]." </h1>";    

    ?>
            </div>
            <div class="col-md-8 col-sm-6" id="navigation">
                <ul>    
                    <li><a href ="index.php">Database Search</a></li>
                    <li><a href="design.php">Database Design</a></li>
                </ul>
            </div>

        </header>

        <?php
    
            echo "<div class ='container backing'>";
            echo "<article class='row searchResultRows' >";
  
            echo "<section class='col-md-7 col-sm-9 col-xs-6'>";
            echo "<h3>Description</h3>";
            echo "<p>" .$displayAnimeInfo["description"]." </p>";
            echo "</section>";
            echo "<section class='col-md-3 col-sm-3 col-xs-6'>";
            echo "<h3>Additional Information</h3>";
            echo "<p>Length: ".$displayAnimeInfo["duration"]. " minutes</p> <p>Start Date: ".$displayAnimeInfo["startDate"]. "</p><p> End Date: ".$displayAnimeInfo["endDate"]. "</p>";
            echo "</section>";
            echo "<section class='col-md-2 col-sm-3 col-xs-4'>";
            echo '<img src="image/'.$displayAnimeInfo["image"].'" alt="thumbnail"/>' ;      
            echo "</section>";
            echo "</article>";          
            echo "</div>";
            
        //arc Title heading
            $arcTitle = $conn->prepare("SELECT `arc`.`arcID`, `arc`.`name` AS arcName
                FROM `arc`
                WHERE (( `arc`.`arcID` = :arcID)) ");

            $arcTitle -> bindValue(":arcID", $arcID);
            $arcTitle -> execute();
            $subTitle = $arcTitle ->fetch();


        ?>
        <div class='container backing'>
            <article class='row'> 
                <section class='col-md-12 col-sm-12 col-xs-12 right-float' id ='arcs'>
                <?php    
                if(($_GET['arcID'] == "null"))
                    {

                    echo "<h4>Please select a season or arc</h4>";

                    } 
                    echo "<h2>".$subTitle["arcName"]."</h2>"; ?>

                </section>
            <?php

            $arcQuery = $conn->prepare("SELECT `arc`.`arcID` AS arcID, `arc`.`name` AS arcName, `arc`.`description` AS arcDescription, `episode`.`name` AS episodeName, `episode`.`description` AS episodeDescription, `arc`.`animeID`, `arc`.`season` AS  arcSeason
            FROM `arc`
            LEFT JOIN `anime` ON `arc`.`animeID` = `anime`.`animeID` 
            LEFT JOIN `episode` ON `arc`.`arcID` = `episode`.`arcID` 
            WHERE  arc.animeID LIKE :animeID GROUP BY season");
            $arcQuery->bindValue(':animeID','%'.$animeID.'%');

            $arcQuery->execute();

            echo "<section class='col-md-3 col-sm-4 col-xs-5' id='rowCount' >"; 
            while ($arc = $arcQuery->fetch())
            {
            echo "<button class ='showDescription'>";
            echo "".$arc["arcSeason"].":".$arc["arcName"]." ";
            echo "</button>";
            echo "<div class = 'description'><p>".$arc["arcDescription"]." </p> ";
                
                
            echo "<a href='details.php?id=".$arc["animeID"]."&arcID=".$arc["arcID"]. "'> 
            <div class='link-format'>
            <P> Episode List</p>
            </div>
            </a>
            </div>"; 

            }

            echo "</section>";

            ?>

    <?php


        $episodeQuery =$conn->prepare("SELECT `arc`.`arcID` AS arcID, `arc`.`name` AS arcName, `arc`.`description` AS arcDescription, `episode`.`name` AS episodeName, `episode`.`description` AS episodeDescription, `arc`.`animeID`, `episode`.`episode` AS episode
        FROM `arc`
        LEFT JOIN `anime` ON `arc`.`animeID` = `anime`.`animeID` 
        LEFT JOIN `episode` ON `arc`.`arcID` = `episode`.`arcID` 
        WHERE  episode.arcID LIKE :arcID GROUP BY episode ASC");
        $episodeQuery->bindValue(':arcID','%'.$arcID.'%');
        $episodeQuery->execute();
                
               echo "<section class='col-md-9 col-sm-8 col-xs-7 right-float'>";
            while ($episode = $episodeQuery->fetch())
            {
                echo "<section class='col-md-12 col-sm-12 col-xs-12 right-float'>";
                echo "<button class ='showDescription'>";    
                echo "".$episode["episode"].":".$episode["episodeName"]."";
                echo "</button>";
                echo "<div class = 'description'> <p>".$episode["episodeDescription"]." </P></div></section>";


            }
                echo "</section>";

             ?>   
                
                
                
                    </article>
 </div> 
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                                   
                     
                     
                     
            <?php         
                     
       /*____________________________________________________________________________*/
                                    
                     
                     
                     
              $relatedAnimeByGenreName = $conn->prepare("SELECT `anime_genre`.`genreID`, `anime_genre`.`animeID`, `genre`.`name`AS genreName, `genre`.`genreID`
                FROM `anime_genre`
                LEFT JOIN `genre` ON `anime_genre`.`genreID` = `genre`.`genreID` 
                WHERE (( `anime_genre`.`animeID` = :relatedAnime))
                GROUP BY `genre`.`genreID` ");


    //link the search string to a varaible and to use it in the search term
   
    $relatedAnimeByGenreName->bindValue(':relatedAnime', $animeID);
    $relatedAnimeByGenreName->execute();

         
    $name = $relatedAnimeByGenreName->fetchAll(PDO::FETCH_COLUMN, 2);
      
    //searches with the GenreName           
           $searchByGenre = array();      
$length = count($name);
                
                for ($i = 0; $i < $length; $i++){
                      array_push($searchByGenre," `genre`.`name` = '" .$name[$i]. "' ");
                    
           
                }
            
    $genreQueryList = implode("OR", $searchByGenre);                
    $PreparedQueryList = "(".$genreQueryList.")";
              
            
             
                
   // need to add all the genreNames into a string with OR and then bind it to genreName

 
 
                $query = "SELECT `genre`.`name` AS  genreName, `genre`.`genreID`, `anime`.`animeID` AS animeID, `anime`.`name` AS animeName, `anime`.`image` AS animeImage
FROM `genre`
LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID`  
WHERE ("   .$PreparedQueryList. ") AND `anime`.`animeID` <>" .$animeID. "
GROUP BY animeName
LIMIT 4";
                

 $relatedAnime = $conn->prepare($query);
    $relatedAnime->execute();
      
 ?>
            
         <div class ='container backing top-margin'>
             <div class="row">
                <header class=col-md-12><h1>Similar Anime</h1></header>
                 </div>
         <article class='row searchResultRows' >
          
  <?php
      while($relatedStuffHere = $relatedAnime->fetch()){
          
        
       
  
             echo "<section class='col-md-3 col-sm-3 col-xs-3 similar-anime'>";
                echo "<p>".$relatedStuffHere["animeName"]."</p>";
          echo "<a href='details.php?id=" . $relatedStuffHere["animeID"] . "&arcID=null'> ";
        echo '<img src="image/' . $relatedStuffHere["animeImage"] . '" alt="thumbnail"/>';
            echo "</a>";
        echo "</section>";
          
        }
    $conn=NULL;
        ?>
             
             </article>
            </div>
        

                     
                     
                     
                     
                     
                     
                     
                     
                     
                     
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
     <script type="text/javascript" src="details.js"></script> 
   