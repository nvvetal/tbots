<?php
class Tyrant_Deck
{
    const BASE64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    /******* CARDS HASH **********/
    private static function chd_f1($id)
    {
        $i = strpos(self::BASE64, $id);
        if ($id == '') return 0;
        return $i;
    }


    private static function chd_f2($id)
    {
        return self::chd_f1(substr($id, 0, 1)) * 64 + self::chd_f1(substr($id, 1, 1));
    }

    private static function chd_to64($x)
    {
        return substr(self::BASE64, $x, 1);
    }

    private static function chd_idto64($id)
    {
        return self::chd_to64(($id >> 6) & 63) . self::chd_to64($id & 63);
    }

    public static function getCardsFromDeckHash($nid, $ordered = true) {
        if ($nid == '') {
            return array();
        }

        if (preg_match('/=(.*?)$/', $nid, $m)) {
            $nid = $m[1];
        }

        $_4000 = '-';
        $cards = array();
        $card = '';
        $p4000 = false;

        while (strlen($nid) > 0) {
            $ch = substr($nid, 0, 1);
            $nid = substr($nid, 1);
            if ($ch != $_4000 && strpos(self::BASE64, $ch) === false) {
                // unknown symbol
                $p4000 = false;
                $card = '';
                continue;
            }

            if ($ch == $_4000) {
                $p4000 = true;
                $card = '';
                continue;
            }
            $card .= $ch;

            if (strlen($card) == 2) {
                $cid = self::chd_f2($card);
                if ($cid > 4000) { // multiplier
                    $cnt = $cid - 4000;
                    if (Tyrant_Cards::getCardById($cid) === false) {
                        continue; // unknown card
                    }

                    if ($cnt > 10) $cnt = 10; // no more than 10 copies for each card
                    $size = count($cards);
                    if ($size != 0) {
                        $cid = $cards[$size - 1];
                        for ($i = 1; $i < $cnt; $i++) {
                            $cards[] = $cid;
                        }
                    }
                } else {
                    if ($p4000) $cid += 4000;
                    if (Tyrant_Cards::getCardById($cid) === false) {
                        continue; // unknown card
                    }
                    $cards[] = $cid;
                }
                $p4000 = false;
                $card = '';
            }
        }

        //if (!$ordered) $cards.sort(function(a, b){return(a-b);});
        if (!$ordered) {
            usort($deck, function($a, $b) {
                return $a - $b;
            });
        }
        $res = array();
        $legendary = false;
        $uniq = array();
        $com = false;
        $l = count($cards);

        for ($i = 0; $i < $l; $i++) {
            if ($cards[$i] >= 1000 && $cards[$i] < 2000) {
                if ($com) continue;
                $com = true;
                array_unshift($res, $cards[$i]);
                continue;
            }

            $cid = $cards[$i];

            $card = Tyrant_Cards::getCardById($cid);
            if ($card === false) {
                continue; // unknown card
            }

            if ($card->getRarity() == 4) {
                if ($legendary) continue;
                $legendary = true;
            } else if ($card->getUnique()) {
                if (!empty($uniq[$cid])) {
                    continue;
                }
                $uniq[$cid] = true;
            }
            $res[] = $cid;
        }

        return $res;
    }

    public static function getDeckHashFromCards($deck, $ordered = true) {
        if ($ordered) {
            usort($deck, function($a, $b) {
                return $a - $b;

            });
        }
        $hash = '';
        $i = 0;
        $_4000 = '-';
        while ($i < count($deck)) {
            $cid = $deck[$i];
            if ($cid > 4999) {
                // ??
                continue;
            }

            if ($cid > 4000) {
                // card with id > 4000
                $hash .= $_4000 . self::chd_idto64($cid - 4000);
            } else {
                $hash .= self::chd_idto64($cid);
            }
            $cnt = 0;
            while ($i < count($deck) && $deck[$i] == $cid) {
                // finding multiplier
                $cnt++;
                $i++;
            }
            if ($cnt > 1) {
                // adding multiplier
                $hash .= self::chd_idto64(4000 + $cnt);
            }
        }

        return $hash;
    }
}