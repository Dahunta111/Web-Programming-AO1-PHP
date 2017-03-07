
 	<!DOCTYPE HTML>
 	<html>
 	<head>
 	<title> Index</title>
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

        
    try {
        $conn = new PDO($dbName, $user, $pass);
    }
    catch (PDOException $exception) {
        echo "Oh no, there was a problem" . $exception->getMessage();
    }
    $query     = "SELECT * FROM anime";
    $results   = $conn->query($query);
    $genreList = $conn->prepare("SELECT * FROM `genre`");
    //link the search string to a varaible and to use it in the search term
    $genreList->execute();

?>
                
    <header class ="container">
        <div class="row">
            <div class="col-md-6 col-sm-6" id="mainTitle">
                <h1>AnimeDB</h1>
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
                <section class="col-md-12 col-sm-12 col-xs-12" id="formLayout">
                    <form action="<?php
                        echo $_SERVER['PHP_SELF'];?>" method="get">
                            <label for="searchBox">Enter a anime name</label>
                                <input id="searchBox" type="text" name="search">
                            <label for ="genre">Select a genre?</label>
                                <select name ="genre" id="genre">

<?php
    while ($genreNames = $genreList->fetch()) {
        echo "<option value='" . $genreNames["name"] . "'>" . $genreNames["name"] . "</option>";
    }
?>
                                </select>
                    <input type="submit" name="submitBtn" value="Search">
                    </form>
                    
                    <a href="index.php"><p class = "show-all-control right-float">Show all</p></a>
                 </section>
            </article>
<?php
if (!isset($_GET['search'])) {
    
    
      $paginationTotalFields = $conn->prepare('SELECT COUNT(*) FROM `genre`
            LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
            LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID` GROUP BY
            `anime`.`name` ');
                 

                 $paginationTotalFields -> execute();
 
$total = $paginationTotalFields->fetchColumn();
            
                 // How many items to list per page
    $pageLimit = 2;
                
                
                
                 // How many pages will there be
    $pages = ceil($total / $pageLimit);

    // What page are we currently on?
    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default'   => 1,
            'min_range' => 1,
        ),
    )));

    // Calculate the offset for the query
    $paginationOffset = ($page - 1)  * $pageLimit;

    // Some information to display to the user
    $startPagination = $paginationOffset + 1;
    $endPagination = min(($paginationOffset + $pageLimit), $total);

   
            
    
    
    
    
    
    
    //basic search when the user hasn't searched yet
    $startQuery = $conn->prepare("SELECT `genre`.`name`, `genre`.`name`, `anime_genre`.`animeID` AS animeID, `anime_genre`.`genreID`, `genre`.`genreID`, `anime`.`animeID`, `anime`.`name` AS animeName, `anime`.`description`,              `anime`.`duration`, `anime`.`startDate`, `anime`.`endDate`, `anime`.`image`
        FROM `genre`
        LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
        LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID`
        GROUP BY `anime`.`name`
        LIMIT
            :limit
        OFFSET
            :offset");
    
           $startQuery->bindParam(':limit', $pageLimit, PDO::PARAM_INT);
    $startQuery->bindParam(':offset', $paginationOffset, PDO::PARAM_INT);
    $startQuery->execute();
    
    while ($start = $startQuery->fetch()) {
        echo "<article class='row searchResultRows' >";
        echo "<section class='col-md-12 col-sm-12 col-xs-12'>";
        echo "<h1>" . $start["animeName"] . " </h1>";
        echo "</section>";
        echo "<section class='col-md-6 col-sm-6 col-xs-6'>";
        echo "<p>" . $start["description"] . " </p>";
        
        echo "<a href='details.php?id=".$start["animeID"]."&arcID=null'><div class='main-page-link'>
        <p> Episode List</p></div></a>";
        
        
        echo "</section>";
        echo "<section class='col-md-3 col-sm-3 col-xs-6'>";
        echo "<p>Length:" . $start["duration"] . " minutes</p> <p>Start Date: " . $start["startDate"] . "</p><p> End Date: " . $start["endDate"] . "</p>";
        echo "</section>";
        echo "<section class='col-md-3 col-sm-3 col-xs-5'>";
        echo '<img src="image/' . $start["image"] . '" alt="thumbnail"/>';
        echo "</section>";
        
        $animeID    = $start["animeID"];
    $genreStart = $conn->prepare("SELECT `genre`.`name` AS genreName, `anime`.`animeID`, `genre`.`name`, `anime_genre`.`animeID` AS animeGenreID, `anime_genre`.`genreID`, `genre`.`genreID` AS GenreID
                                FROM `genre`
                                LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
                                LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID` WHERE `anime`.`animeID` = :animeID GROUP BY genreName");
        $genreStart->bindValue(':animeID', $animeID);      
        $genreStart->execute();
        
        echo "<section class='col-md-12 col-sm-12 col-xs-6'> ";
        
            while ($genreList = $genreStart->fetch()) {
                echo " <p class = 'genre'> " . $genreList["genreName"] . " </p> ";
            }
        
        echo "</section>";
        echo "</article>";
        
        
    }
            // The "back" link
    $prevlink = ($page > 1) ? '
    <a href="index.php?&page=1  "title="First page">&laquo;</a> 
     <a href="index.php?&page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

    // The "forward" link
    $nextlink = ($page < $pages) ? '
    <a href="index.php?&page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> 
    <a href="index.php?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

    // Display the paging information
    echo '<footer class="row"><div id="pageDisplay"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $startPagination, '-', $endPagination, ' of ', $total, ' results ', $nextlink, ' </p></div></footer>';
       
     
            
    //end script and create final tags
        echo "</div>";
        echo "</body>";
        echo "</html>";
        exit;
}
            
            
    //get the search term in URL
    $searchTerm  = $_GET['search'];
    $genreName   = $_GET['genre'];
            
            
            
            
            
            
                $paginationTotalFields = $conn->prepare('SELECT COUNT(*) FROM `genre`
            LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
            LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID` WHERE  `genre`.`name` = :genreSearch  and  `anime`.`name` LIKE :searchTerm');
                 
    $paginationTotalFields->bindValue(':searchTerm', '%' . $searchTerm . '%');
    $paginationTotalFields->bindValue(':genreSearch', $genreName);
                 $paginationTotalFields -> execute();
 
$total = $paginationTotalFields->fetchColumn();
            
                 // How many items to list per page
    $pageLimit = 2;
                
                
                
                 // How many pages will there be
    $pages = ceil($total / $pageLimit);

    // What page are we currently on?
    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default'   => 1,
            'min_range' => 1,
        ),
    )));

    // Calculate the offset for the query
    $paginationOffset = ($page - 1)  * $pageLimit;

    // Some information to display to the user
    $start = $paginationOffset + 1;
    $endPagination = min(($paginationOffset + $pageLimit), $total);

   
            
            
            
            
            
            
            
            
            
            
            
    //prepare a string
    $searchAnime = $conn->prepare("SELECT `genre`.`name`, `genre`.`name`, `anime_genre`.`animeID` AS animeID, `anime_genre`.`genreID`, `genre`.`genreID`, `anime`.`animeID`, `anime`.`name` AS animeName, `anime`.`description`,              `anime`.`duration`, `anime`.`startDate`, `anime`.`endDate`, `anime`.`image`
            FROM `genre`
            LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
            LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID` WHERE  `genre`.`name` = :genreSearch  and  `anime`.`name` LIKE :searchTerm
            GROUP BY `anime`.`name` 
            LIMIT
            :limit
        OFFSET
            :offset");


    //link the search string to a varaible and to use it in the search term
    $searchAnime->bindValue(':searchTerm', '%' . $searchTerm . '%');
    $searchAnime->bindValue(':genreSearch', $genreName);
            //for Pagination
            $searchAnime->bindParam(':limit', $pageLimit, PDO::PARAM_INT);
    $searchAnime->bindParam(':offset', $paginationOffset, PDO::PARAM_INT);
    $searchAnime->execute();

            
    $count = $conn->prepare("SELECT COUNT(*) FROM `genre`
            LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
            LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID` WHERE  `genre`.`name` = :genreSearch  and  `anime`.`name` LIKE :searchTerm ");
    $count->bindValue(':searchTerm', '%' . $searchTerm . '%');
    $count->bindValue(':genreSearch', $genreName);
    $count->execute();
 
        //row Counter
        $number_of_rows = $count->fetchColumn();
            echo "<section id='rowCount' class='col-md-12 col-sm-12'><p>results: ";
            echo $number_of_rows;
            echo "</p>";
            echo "</section>";

    while ($anime = $searchAnime->fetch()) {
        echo "<article class='row searchResultRows' >";
        echo "<section class='col-md-12 col-sm-12'>";
        echo "<h1>" . $anime["animeName"] . " </h1>";
        echo "</section>";
        echo "<section class='col-md-6 col-sm-6'>";
        echo "<p>" . $anime["description"] . " </p>";

        echo "<a href='details.php?id=".$anime["animeID"]."&arcID=null'><div class='main-page-link'>
        <p> Episode List</p></div></a>";

        echo "</section>";
        echo "<section class='col-md-3 col-sm-3'>";
        echo "<p>" . $anime["duration"] . " minutes</p> <p>Start Date: " . $anime["startDate"] . "</p><p> End Date: " . $anime["endDate"] . "</p>";
        echo "</section>";
        echo "<section class='col-md-3 col-sm-5'>";
        echo '<img src="image/' . $anime["image"] . '" alt="thumbnail"/>';
        echo "</section>";

        $genre   = $conn->prepare("SELECT `genre`.`name` AS genreName, `anime`.`animeID`, `genre`.`name`, `anime_genre`.`animeID` AS animeGenreID, `anime_genre`.`genreID`, `genre`.`genreID` AS GenreID
                                        FROM `genre`
                                        LEFT JOIN `anime_genre` ON `genre`.`genreID` = `anime_genre`.`genreID` 
                                        LEFT JOIN `anime` ON `anime_genre`.`animeID` = `anime`.`animeID` WHERE `anime`.`animeID` = :animeID GROUP BY genreName");
        //needs fixing 
        $animeID = $anime["animeID"];
        $genre->bindValue(':animeID', $animeID, PDO::PARAM_INT);
        $genre->execute();
        echo "<section class='col-md-12 col-sm-12'> ";
        
        while ($genreList = $genre->fetch()) {
            echo " <p class = 'genre'> " . $genreList["genreName"] . " </p> ";
        }
        
    echo "</section>";
    echo "</article>";
       
}
    
        
            
        // The "back" link
    $prevlink = ($page > 1) ? '
    <a href="index.php?search='.$searchTerm.'&genre='.$genreName.'&page=1"   "title="First page">&laquo;</a> 
     <a href="index.php?search='.$searchTerm.'&genre='.$genreName.'&page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

    // The "forward" link
    $nextlink = ($page < $pages) ? '
    <a href="index.php?search='.$searchTerm.'&genre='.$genreName.'&page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> 
    <a href="index.php?search='.$searchTerm.'&genre='.$genreName.'&page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

    // Display the paging information
            
    echo '<footer class="row"><div id="pageDisplay"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $endPagination, ' of ', $total, ' results ', $nextlink, ' </p></div></footer>';
       
     
            
  
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
$conn = NULL;
            
?>
            
          
        </div>
    </body>
 </html>