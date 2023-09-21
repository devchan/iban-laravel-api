<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IbanNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->ibanCheker($value)) {
            $fail('The :attribute must be valid.');
        }
    }

    public function ibanCheker($input)
    {
        $iban = strtolower($input);

        // The official min length is 5. Also prevents substringing too short input.
        if(strlen($iban) < 5) return false;

        // lengths of iban per country
        $countries = array(
            'al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,
            'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,
            'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,
            'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,
            'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24
        );
        // subsitution scheme for letters
        $chars = array(
            'a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,
            'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35
        );

        // Check input country code is known
        if (!isset($countries[ substr($iban,0,2) ])) return false;

        // Check total length for given country code
        if (strlen($iban) != $countries[ substr($iban,0,2) ]) { return false; }

        // Move first 4 chars to end
        $movedChar = substr($iban, 4) . substr($iban,0,4);

        // Replace letters by their numeric variant
        $movedCharArray = str_split($movedChar);
        $newString = "";
        foreach ($movedCharArray as $k => $v) {
            if ( !is_numeric($movedCharArray[$k]) ) {
                // if any other cahracter then the known letters, its bogus
                if(!isset($chars[$movedCharArray[$k]])) return false;
                $movedCharArray[$k] = $chars[$movedCharArray[$k]];
            }
            $newString .= $movedCharArray[$k];
        }

        // Now we just need to validate the checksum
        // Use bcmod if available
        if (function_exists("bcmod")) { return bcmod($newString, '97') == 1; }

        // Else use this workaround
        // http://au2.php.net/manual/en/function.bcmod.php#38474
        $x = $newString;
        $y = "97";
        $take = 5;
        $mod = "";
        do {
            $a = (int)$mod . substr($x, 0, $take);
            $x = substr($x, $take);
            $mod = $a % $y;
        }
        while (strlen($x));
        return (int)$mod == 1;

    }
}
