<?php
/**
 * Simple PO to MO compiler
 * Compiles .po files to .mo format for GLPI
 */

function compilePOtoMO($poFile, $moFile) {
    $po = file_get_contents($poFile);
    $lines = explode("\n", $po);
    
    $entries = [];
    $msgid = '';
    $msgstr = '';
    $inMsgid = false;
    $inMsgstr = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line) || $line[0] === '#') {
            continue;
        }
        
        if (strpos($line, 'msgid ') === 0) {
            if ($msgid && $msgstr) {
                $entries[$msgid] = $msgstr;
            }
            $msgid = substr($line, 7, -1);
            $msgstr = '';
            $inMsgid = true;
            $inMsgstr = false;
        } elseif (strpos($line, 'msgstr ') === 0) {
            $msgstr = substr($line, 8, -1);
            $inMsgid = false;
            $inMsgstr = true;
        } elseif ($line[0] === '"' && $line[strlen($line) - 1] === '"') {
            $content = substr($line, 1, -1);
            if ($inMsgid) {
                $msgid .= $content;
            } elseif ($inMsgstr) {
                $msgstr .= $content;
            }
        }
    }
    
    if ($msgid && $msgstr) {
        $entries[$msgid] = $msgstr;
    }
    
    // Create MO file
    $mo = '';
    
    // Magic number
    $mo .= pack('L', 0x950412de);
    
    // Format revision
    $mo .= pack('L', 0);
    
    // Number of strings
    $count = count($entries);
    $mo .= pack('L', $count);
    
    // Offset of table with original strings
    $originals_offset = 28;
    $mo .= pack('L', $originals_offset);
    
    // Offset of table with translation strings
    $translations_offset = $originals_offset + ($count * 8);
    $mo .= pack('L', $translations_offset);
    
    // Size of hashing table (not used)
    $mo .= pack('L', 0);
    
    // Offset of hashing table (not used)
    $mo .= pack('L', 0);
    
    $originals_table = '';
    $translations_table = '';
    $strings = '';
    $strings_offset = $translations_offset + ($count * 8);
    
    foreach ($entries as $original => $translation) {
        // Add original string
        $originals_table .= pack('L', strlen($original));
        $originals_table .= pack('L', $strings_offset);
        $strings_offset += strlen($original) + 1;
        
        // Add translation string
        $translations_table .= pack('L', strlen($translation));
        $translations_table .= pack('L', $strings_offset);
        $strings_offset += strlen($translation) + 1;
        
        // Add strings
        $strings .= $original . "\0";
        $strings .= $translation . "\0";
    }
    
    $mo .= $originals_table;
    $mo .= $translations_table;
    $mo .= $strings;
    
    file_put_contents($moFile, $mo);
    echo "✓ Compiled: $moFile\n";
}

// Compile pt_BR
$localesDir = __DIR__;
compilePOtoMO(
    $localesDir . '/pt_BR.po',
    $localesDir . '/pt_BR.mo'
);

echo "\n✓ Portuguese (Brazil) locale compiled successfully!\n";
