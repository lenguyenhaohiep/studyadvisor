<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MatrixFactorization {
    /*
     * Input
     */

    public $numItem;
    public $numUser;
    public $numRating;
    public $rating;
    public $avgScore = 0;
    public $factor = 5;
    public $maxIteration = 100;
    public $GAMA = 0.001;
    public $lamda4 = 0.05;
    /*
     * Output
     */
    public $biasUser;
    public $biasItem;
    public $userFactorVector;
    public $itemFactorVector;

    public function initialize() {
        $count = 0;
        if ($this->avgScore == 0) {
            $this->numRating = 0;
            foreach ($this->rating as $r) {
                if ($r > 0) {
                    $count++;
                    $this->avgScore += $r;
                }
            }
            $this->avgScore = $this->avgScore / $count;
            $this->numRating = $count;
        }
    }

    /*
     * Initialize Factor vector for all users and items
     */

    public function initializeFactorVectors() {
        $this->userFactorVector = array();
        $this->itemFactorVector = array();
        $initialVector = array();
        for ($f = 0; $f < $this->factor; $f++) {
            $initialVector[] = $f * 0.009 + 0.01;
        }

        for ($i = 0; $i < $this->numItem; $i++) {
            $this->itemFactorVector[] = $initialVector;
        }
        for ($u = 0; $u < $this->numUser; $u++) {
            $this->userFactorVector[] = $initialVector;
        }
    }

    /*
     * Rating score is predicted by MFCF
     */

    public function prediction($u, $i) {
        $Pu = $this->userFactorVector[$u];
        $Qi = $this->itemFactorVector[$i];
        $predictedRating = $this->avgScore + $this->biasUser[$u] + $this->biasItem[$i] + $this->dot($Pu, $Qi);
        return $predictedRating;
    }

    /*
     * Calculate bias
     */

    public function baselinePredictorBias() {
        $lamda2 = (float) $this->numRating / $this->numItem;
        $lamda3 = (float) $this->numRating / $this->numUser;
        $this->biasItem = array();
        $this->biasUser = array();

        //Calculate bias for Item;
        for ($i = 0; $i < $this->numItem; $i++) {
            $this->biasItem[$i] = 0;
            $numRatedItem = 0;
            $sumOfErrors = 0;
            for ($u = 0; $u < $this->numUser; $u++) {
                if (isset($this->rating[$u . "-" . $i])) {
                    $ratedValue = $this->rating[$u . "-" . $i];
                    if (isset($ratedValue)) {
                        $numRatedItem++;
                        $sumOfErrors += $ratedValue - $this->avgScore;
                    }
                }
            }
            $this->biasItem[$i] = $sumOfErrors / ($lamda2 + $numRatedItem);
        }

        //Calculate bias for User;
        for ($u = 0; $u < $this->numUser; $u++) {
            $this->biasUser[$u] = 0;
            $numRatedUser = 0;
            $sumOfErrors = 0;
            for ($i = 0; $i < $this->numItem; $i++) {
                if (isset($this->rating[$u . "-" . $i])) {
                    $ratedValue = $this->rating[$u . "-" . $i];
                    if ($ratedValue > 0) {
                        $numRatedUser++;
                        $sumOfErrors += $ratedValue - $this->avgScore - $this->biasItem[$i];
                    }
                }
            }
            $this->biasUser[$u] = $sumOfErrors / ($lamda3 + $numRatedUser);
        }
    }

    /*
     * Learn Model
     */

    public function SVD() {
        $iterator = 0;
        $epsilon = 0.000001;
        $oldSumErrors = 0;
        $sumErrors = 0;

        while ($iterator < $this->maxIteration) {
//            System.out.println("Iterator = " + iterator);
            $iterator++;
            for ($u = 0; $u < $this->numUser; $u++) {
                for ($i = 0; $i < $this->numItem; $i++) {
                    if (isset($this->rating[$u . "-" . $i])) {
                        $ratedValue = $this->rating[$u . '-' . $i];
                        if (isset($ratedValue)) {
                            $error = $ratedValue - $this->prediction($u, $i);
                            $this->biasUser[$u] = $this->biasUser[$u] + $this->GAMA * ($error - $this->lamda4 * $this->biasUser[$u]);
                            $this->biasItem[$i] = $this->biasItem[$i] + $this->GAMA * ($error - $this->lamda4 * $this->biasItem[$i]);
                            $Pu = $this->userFactorVector[$u];
                            $Qi = $this->itemFactorVector[$i];
                            $_Pu = $this->VsV($this->NxV($error, $Qi), $this->NxV($this->lamda4, $Pu));
                            $_Qi = $this->VsV($this->NxV($error, $Pu), $this->NxV($this->lamda4, $Qi));
                            $this->itemFactorVector[$i] = $this->VpV($Qi, $this->NxV($this->GAMA, $_Qi));
                            $this->userFactorVector[$u] = $this->VpV($Pu, $this->NxV($this->GAMA, $_Pu));
                        }
                    }
                }
            }

            //Compute sum of errors
            $minf = 0;
            for ($u = 0; $u < $this->numUser; $u++) {
                for ($i = 0; $i < $this->numItem; $i++) {
                    if (isset($this->rating[$u . "-" . $i])) {
                        $ratedValue = $this->rating[$u . '-' . $i];
                        if (isset($ratedValue)) {
                            $minf +=
                                    pow($ratedValue - $this->prediction($u, $i), 2)
                                    + $this->lamda4 * (pow($this->biasItem[$i], 2)
                                    + pow($this->biasUser[$u], 2)
                                    + pow($this->module($this->itemFactorVector[$i]), 2)
                                    + pow($this->module($this->userFactorVector[$u]), 2));
                        }
                    }
                }
            }

            $oldSumErrors = $sumErrors;
            $sumErrors = $minf;

            if (abs($sumErrors - $oldSumErrors) < $epsilon) {

                break;
            }
        }
    }

    public function run() {
        $this->initialize();
        $this->initializeFactorVectors();
        $this->baselinePredictorBias();
        $this->SVD();
    }

    /*
     * Matrix and Vector Operations
     */

    public function dot($a, $b) {
        $length = count($a);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result += $a[$i] * $b[$i];
        }
        return $result;
    }

    public function NxV($number, $v) {
        $_v = array();
        for ($i = 0; $i < count($v); $i++) {
            $_v[$i] = $number * $v[$i];
        }
        return $_v;
    }

    public function VpV($v1, $v2) {
        $_v = array();
        for ($i = 0; $i < count($v1); $i++) {
            $_v[$i] = $v1[$i] + $v2[$i];
        }
        return $_v;
    }

    public function VsV($v1, $v2) {
        $_v = array();
        for ($i = 0; $i < count($v1); $i++) {
            $_v[$i] = $v1[$i] - $v2[$i];
        }
        return $_v;
    }

    public function module($v) {
        $s = 0;
        for ($i = 0; $i < count($v); $i++) {
            $s += pow($v[$i], 2);
        }
        return sqrt($s);
    }

}

?>
