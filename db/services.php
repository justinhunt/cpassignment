<?php
/**
 * Services definition.
 *
 * @package mod_readaloud
 * @author  Justin Hunt - poodll.com
 */

$functions = array(

    'mod_cpassignment_submit_rec' => array(
            'classname'   => 'mod_cpassignment_external',
            'methodname'  => 'submit_rec',
            'description' => 'submits recording.',
            'capabilities'=> 'mod/cpassignment:view',
            'type'        => 'write',
            'ajax'        => true,
    ),

    'mod_cpassignment_remove_rec' => array(
            'classname'   => 'mod_cpassignment_external',
            'methodname'  => 'remove_rec',
            'description' => 'removes recording.',
            'capabilities'=> 'mod/cpassignment:view',
            'type'        => 'write',
            'ajax'        => true,
    ),

    'mod_cpassignment_submit_attempt' => array(
            'classname'   => 'mod_cpassignment_external',
            'methodname'  => 'submit_attempt',
            'description' => 'submits regular attempt.',
            'capabilities'=> 'mod/cpassignment:view',
            'type'        => 'write',
            'ajax'        => true,
    ),

    'mod_cpassignment_select_attempt' => array(
        'classname'   => 'mod_cpassignment_external',
        'methodname'  => 'select_attempt',
        'description' => 'selects attempt.',
        'capabilities'=> 'mod/cpassignment:view',
        'type'        => 'write',
        'ajax'        => true,
    )
);