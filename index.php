<?php

class Cell{
    Public $X;
    Public $Y;
    Public $isWall;
    Public $id;

    function __construct($xPos, $yPos){
        $this->X = $xPos;
        $this->Y = $yPos;
    }

    //makes the cell a wall
    function makeWall(){
        $this->isWall = TRUE;
    }

    //makes the cell a path
    function makePath(){
        $this->isWall = FALSE;
    }

    /*returns how many cells this cell divides
    *takes in an array to check cell in
    */
    function DividesHowMany($cellsArray){
        $positions = array(
            New Cell($this->X, $this->Y + 1),
            New Cell($this->X, $this->Y - 1),
            New Cell($this->X + 1, $this->Y),
            New Cell($this->X - 1, $this->Y));
        $counter = 0;
        foreach($positions as &$c){
            if(!empty($cellsArray[$c->X][$c->Y]))
            {
                if($cellsArray[$c->X][$c->Y]->isWall == FALSE)
                {
                    $counter++;
                }
            }
            
        }
        unset($c);
        return $counter;
    }

    //sets the id of the cell to the $n formal parameter
    function setId($n){
        $this->id = $n;
    }
}
?>

<?php 
class Grid{
    Public $Width;
    Public $Height;
    Public $Cells = array();
    Public $Walls = array();

    function __construct($width, $height){
        $this->Width = $width;
        $this->Height = $height;
    }

    //fills the grid with walls
    function fillWithWalls(){
        for($i = 0; $i <= $this->Height; $i++){
            array_push($this->Cells, array());
            for($j = 0; $j <= $this->Height; $j++){
                $tempCell = new Cell($i,$j);
                $tempCell->makeWall();
                $this->Cells[$i][$j] = $tempCell;
            }
        }
    }    

    //gets the neigbours of a given cell
    function getNeighbours($c){
        $n = array();
        $positions = array(
            New Cell($c->X, $c->Y + 1),
            New Cell($c->X, $c->Y - 1),
            New Cell($c->X + 1, $c->Y),
            New Cell($c->X - 1, $c->Y));
        
        foreach($positions as &$value){
            if(!empty($this->Cells[$value->X][$value->Y])){
                array_push($n, $this->Cells[$value->X][$value->Y]);
            }
        }
        unset($value);
        return $n;
    }

    //returns a random wall from the walls array
    function getRandomWall(){
        $randIndex = rand(0, count($this->Walls)-1);
        return array($this->Walls[$randIndex], $randIndex);
    }
}
?>

<?php 

//removes a value at an index from a list 
function remove(&$a, $index){
    $temp = array();
    unset($a[$index]);
    foreach($a as &$val) array_push($temp, $val);
    $a = $temp;
}
?>
<?php 
//https://en.wikipedia.org/wiki/Maze_generation_algorithm : see randomized prims algorithm
$myGrid = new Grid(50,50); 
$myGrid->fillWithWalls(); // start with a grid full of walls
$randomX = rand(0,$myGrid->Height); 
$randomY = rand(0,$myGrid->Width);
$myGrid->Cells[$randomX][$randomY]->makePath(); //pick a cell 

$neib = $myGrid->getNeighbours($myGrid->Cells[$randomX][$randomY]); //get the neigbours of the cell

foreach($neib as &$c){ //add the neigbours of the cell to the wall list
    array_push($myGrid->Walls, $c); 
}
unset($c);
$cellNumber = 0;
while (count($myGrid->Walls) > 0){
    $thisWall = $myGrid->getRandomWall(); //pick a random wall from the list
    if($thisWall[0]->DividesHowMany($myGrid->Cells) == 1){ //if only one of the two cells the wall divides is visited
        $myGrid->Cells[$thisWall[0]->X][$thisWall[0]->Y]->makePath(); //make the wall a path 
        $myGrid->Cells[$thisWall[0]->X][$thisWall[0]->Y]->setId($cellNumber);
        $cellNumber++;
        $walls = $myGrid->getNeighbours($myGrid->Cells[$thisWall[0]->X][$thisWall[0]->Y]);
        foreach($walls as &$c){ //add the neigbouring walls of the cell to the wall list 
            array_push($myGrid->Walls, $c);
        }
        unset($c);
        

    }
    remove($myGrid->Walls,$thisWall[1]); //remove the wall from the list
}


?>
<!DOCTYPE html>
<html>
<head>
<style>
    /* class that animates the rectangle to be visible from inveisible */
    .Visible{
        -webkit-animation-fill-mode: forwards; /* Chrome 16+, Safari 4+ */
        -moz-animation-fill-mode: forwards;    /* FF 5+ */
        -o-animation-fill-mode: forwards;      /* Not implemented yet */
        -ms-animation-fill-mode: forwards;     /* IE 10+ */
        animation-fill-mode: forwards;  
        -webkit-animation-name: example; /* Safari 4.0 - 8.0 */
        -webkit-animation-duration: .1s; /* Safari 4.0 - 8.0 */   
        animation-name: example;
        animation-duration: 1s;
    }



    /* Safari 4.0 - 8.0 */
    @-webkit-keyframes example {
        from {opacity: 0;}
        to {opacity: 1;}
    }

    /* Standard syntax */
    @keyframes example {
        from {opacity: 0;}
        to {opacity: 1;}
    }
</style>
<script>
            //called when the svg is clicked it starts the animation that shows cells becoming paths 
            function animateThatStuff() {
                let max = <?php echo (string)$cellNumber; ?>;
                for(let i = 0; i < max; i++){
                    let x = document.getElementsByClassName(i.toString());
                    console.log(x);
                    x[0].style.opacity = "0";
                }  
                console.log("works");
                for(let i = 0; i < max; i++){
                    let y = document.getElementsByClassName(i.toString());
                    y[0].style.animationDelay = (i/10).toString().concat("s");
                    y[0].classList.add("Visible");
                }  
               
            };

</script>
</head>
<body>

<p>This is a 50 by 50 maze generated with prims algorithm. Click the maze to watch it generate</p>
<p> source code at <a href="https://github.com/LorneBarnaby/maze.lorneb.com">My Github</a></p><br>
<br>



<?php 
    //generates svg from the List of cells in the Grid
    echo "<svg onclick=\"animateThatStuff()\" width=\"". (string)(16 *  $myGrid->Width) . "\"";
    echo " height=\"". (string)(16 *  $myGrid->Height) . "\">";
    for($i = 0; $i <= $myGrid->Height; $i++){
        for($j = 0; $j <= $myGrid->Height; $j++){   
            echo "<rect width=\"15\" height=\"15\"";
            echo " x=\"" . (string)(($j * 15)) . "\"";
            echo " y=\"" . (string)(($i * 15)). "\"";
            if($myGrid->Cells[$i][$j]->isWall == FALSE){
                echo " fill=\"black\"";
                echo " class=\"" . (string)$myGrid->Cells[$i][$j]->id . "\"";
            } else {
                echo " fill=\"white\"";
            }
            echo "/>";
        }

    }
    echo "</svg>"
?>

</body>
</html>
