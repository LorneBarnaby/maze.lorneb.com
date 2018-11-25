<!DOCTYPE html>
<html>
<body>
<p>This is a 50 by 50 maze generated with prims algorithm</p>
<?php
class Cell{
    Public $X;
    Public $Y;
    Public $isWall;

    function __construct($xPos, $yPos){
        $this->X = $xPos;
        $this->Y = $yPos;
    }

    function makeWall(){
        $this->isWall = TRUE;
    }

    function makePath(){
        $this->isWall = FALSE;
    }

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

    function getRandomWall(){
        $randIndex = rand(0, count($this->Walls)-1);
        return array($this->Walls[$randIndex], $randIndex);
    }
}
?>

<?php 
function remove(&$a, $index){
    $temp = array();
    unset($a[$index]);
    foreach($a as &$val) array_push($temp, $val);
    $a = $temp;
}
?>
<?php 

$myGrid = new Grid(50,50);
$myGrid->fillWithWalls();
$randomX = rand(0,$myGrid->Height);
$randomY = rand(0,$myGrid->Width);
$myGrid->Cells[$randomX][$randomY]->makePath();

$neib = $myGrid->getNeighbours($myGrid->Cells[$randomX][$randomY]);

foreach($neib as &$c){
    array_push($myGrid->Walls, $c);
}
unset($c);

while (count($myGrid->Walls) > 0){
    $thisWall = $myGrid->getRandomWall();
    if($thisWall[0]->DividesHowMany($myGrid->Cells) == 1){
        $myGrid->Cells[$thisWall[0]->X][$thisWall[0]->Y]->makePath();
        $walls = $myGrid->getNeighbours($myGrid->Cells[$thisWall[0]->X][$thisWall[0]->Y]);
        foreach($walls as &$c){
            array_push($myGrid->Walls, $c);
        }
        unset($c);
        

    }
    remove($myGrid->Walls,$thisWall[1]);
}


?>


<?php 
    echo "<svg width=\"". (string)(19 *  $myGrid->Width) . "\"";
    echo " height=\"". (string)(19 *  $myGrid->Height) . "\">";
    for($i = 0; $i <= $myGrid->Height; $i++){
        for($j = 0; $j <= $myGrid->Height; $j++){   
            echo "<rect width=\"15\" height=\"15\"";
            echo " x=\"" . (string)(($j + $j * 15) - 1) . "\"";
            echo " y=\"" . (string)(($i + $i * 15) - 1). "\"";
            if($myGrid->Cells[$i][$j]->isWall == FALSE){
                echo " fill=\"black\"";
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
