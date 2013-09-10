<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class STI {
    /*
     * Fields
     */

    public $ratings;
    public $itemProfiles;
    public $itemProfileTags;
    public $numItems;
    public $numContexts;
    public $numUsers;
    public $clusters;
    public $numNode;
    public $graph = array();
    public $matrix2d;
    public $biasContext;

    //Step 1
    public function buildItemProfiles() {
        $this->itemProfiles = array();
        $this->itemProfileTags = array();
        for ($i = 0; $i < $this->numItems; $i++) {
            for ($c = 0; $c < $this->numContexts; $c++) {
                $itemprofile = array();
                for ($u = 0; $u < $this->numUsers; $u++) {
                    $itemprofile[$u] = 0;
                    if (isset($this->ratings[$u . "-" . $i . "-" . $c]))
                        $itemprofile[$u] = $this->ratings[$u . "-" . $i . "-" . $c];
                }
                $this->itemProfiles[] = $itemprofile;
                $this->itemProfileTags[] = $i . '-' . $c;
            }
        }
    }

    public function computeWeigh($a, $b) {
        $x = 0;
        $y = 0;
        $z = 0;
        $weigh = 0;
        for ($i = 0; $i < $this->numUsers; $i++) {
            $vala = $this->itemProfiles[$a][$i];
            $valb= $this->itemProfiles[$b][$i];
            if ($vala > 0 && $valb > 0) {
                $x += $vala*$valb;
                $y += $vala*$vala;
                $z += $valb*$valb;
            }
        }
        if ($y == 0 || $z == 0) {
            $weigh = 0;
        } else {
            $weigh = ($x / (sqrt($z) * sqrt($y)));
        }
        return $weigh;
    }

    public function BFS($start) {
        $front = 0;
        $rear = 0;
        $trace = array();
        $queue = array();
        $queue[0] = $start;
        for ($i = 0; $i < $this->numNode; $i++) {
            $trace[$i] = true;
        }
        $trace[$start] = false;
        do {
            $u = $queue[$front];
            $front++;
            for ($v = 0; $v < $this->numNode; $v++) {
                if ($this->graph[$u . "-" . $v] == TRUE && $trace[$v] == TRUE) {
                    $rear++;
                    $queue[$rear] = $v;
                    $trace[$v] = FALSE;
                }
            }
        } while (($front <= $rear) && ($rear < $this->numNode - 1));
        $res = array();
        for ($i = 0; $i < $this->numNode; $i++) {
            if ($trace[$i] == FALSE) {
                $res[] = $i;
            }
        }
        return $res;
    }

    private function findConnectedComponents() {
        $free = array();
        for ($i = 0; $i < $this->numNode; $i++) {
            $free[$i] = TRUE;
        }
        $level2 = array();
        for ($u = 0; $u < $this->numNode; $u++) {
            if ($free[$u] == TRUE) {
                $level1 = $this->BFS($u);
                $level2[] = $level1;
                for ($i = 0; $i < count($level1); $i++) {
                    $free[$level1[$i]] = FALSE;
                }
            }
        }
        return $level2;
    }

    //Step 2 function
    public function cluster() {
        $this->numNode = count($this->itemProfiles);
        $gweigh = array();
        
        for ($i = 0; $i < $this->numNode; $i++) {
            $min = -1;
            $index = $i;
            for ($j = 0; $j < $this->numNode; $j++) {
                $this->graph[$i . "-" . $j] = false;
                if ($i != $j) {
                    $weigh = 0;
                    if (!isset($gweigh[$i . "-" . $j])) {
                        $weigh = $this->computeWeigh($i, $j);
                        $gweigh[$i . "-" . $j] = $weigh;
                        $gweigh[$j . "-" . $i] = $weigh;
                    } else {
                        $weigh = $gweigh[$i . "-" . $j];
                    }
                    if ($weigh > $min) {
                        $min = $weigh;
                        $index = $j;
                    }
                } else {
                    $this->graph[$i . "-" . $j] = true;
                }
            }
            $this->graph[$i . "-" . $index] = true;
            $this->graph[$index . "-" . $i] = true;
        }
        $this->clusters = $this->findConnectedComponents();
    }

    public function getNewRatingVector($cluster) {
        $n = count($this->itemProfiles[0]);
        $numbers = array();
        $vals = array();
        for ($i = 0; $i < $n; $i++) {
            $numbers[] = 0;
            $vals[] = 0;
        }

        $numCluster=count($cluster);
        for ($i = 0; $i < $numCluster; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($this->itemProfiles[$cluster[$i]][$j] > 0) {
                    $vals[$j] = $vals[$j] + $this->itemProfiles[$cluster[$i]][$j];
                    $numbers[$j]++;
                }
            }
        }
        for ($i = 0; $i < $n; $i++) {
            if ($numbers[$i] > 0) {
                $vals[$i] /= $numbers[$i];
            }
        }
        return $vals;
    }

    //Step 3
    public function build2dMatrix() {
        $numrow = count($this->clusters);
        $this->matrix2d = array();
        for ($i = 0; $i < $numrow; $i++) {
            $this->matrix2d[] = $this->getNewRatingVector($this->clusters[$i]);
        }
    }

    //Step 4 analyzing context affect
    public function analyzeContextEffect() {
        for ($u = 0; $u < $this->numUsers; $u++) {
            for ($c = 0; $c < $this->numContexts; $c++) {
                $val = 0.0;
                $k = $u . "-" . $c;
                $sumAll = 0;
                $sumContext = 0;
                $countAll = 0;
                $countContext = 0;

                foreach ($this->ratings as $key => $r) {
                    $temkey = explode("-", $key);
                    if ($temkey[0] == $u) {
                        $countAll++;
                        $sumAll+=1;

                        if ($temkey[2] == $c) {
                            $countContext++;
                            $sumContext+=$r;
                        }
                    }
                }


                if ($sumContext > 0) {
                    $val = ($sumContext / $countContext) - ($sumAll / $countAll);
                }
                $this->biasContext[$k] = $val;
            }
        }
    }

    public function run() {
        $this->buildItemProfiles();
        $this->cluster();
//        $this->build2dMatrix();
    }

}

?>
