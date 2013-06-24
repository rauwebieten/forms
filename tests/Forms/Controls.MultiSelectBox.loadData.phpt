<?php

/**
 * Test: Nette\Forms\Controls\MultiSelectBox.
 *
 * @author     Martin Major
 * @package    Nette\Forms
 */

use Nette\Forms\Form;



require __DIR__ . '/../bootstrap.php';



$_SERVER['REQUEST_METHOD'] = 'POST';

$_POST = array(
	'string1' => 'red-dwarf',
	'string2' => 'days-of-our-lives',
	'multi' => array('red-dwarf', 'unknown', 0),
	'zero' => 0,
	'empty' => '',
	'malformed' => array(array(NULL)),
);

$series = array(
	'red-dwarf' => 'Red Dwarf',
	'the-simpsons' => 'The Simpsons',
	0 => 'South Park',
	'' => 'Family Guy',
);


test(function() use ($series) {
	$form = new Form;
	$input = $form->addMultiSelect('string1', NULL, $series);

	Assert::true( $form->isValid() );
	Assert::same( array('red-dwarf'), $input->getValue() );
	Assert::same( array('red-dwarf' => 'Red Dwarf'), $input->getSelectedItem() );
	Assert::true( $input->isFilled() );
});



test(function() use ($series) { // invalid input
	$form = new Form;
	$input = $form->addMultiSelect('string2', NULL, $series);

	Assert::true( $form->isValid() );
	Assert::same( array(), $input->getValue() );
	Assert::same( array(), $input->getSelectedItem() );
	Assert::false( $input->isFilled() );
});



test(function() use ($series) { // multiple selected items
	$form = new Form;
	$input = $form->addMultiSelect('multi', NULL, $series);

	Assert::true( $form->isValid() );
	Assert::same( array('red-dwarf', 0), $input->getValue() );
	Assert::same( array('red-dwarf' => 'Red Dwarf', 0 => 'South Park'), $input->getSelectedItem() );
	Assert::true( $input->isFilled() );
});



test(function() use ($series) {
	$form = new Form;
	$input = $form->addMultiSelect('zero', NULL, $series);

	Assert::true( $form->isValid() );
	Assert::same( array(0), $input->getValue() );
	Assert::same( array(0), $input->getRawValue() );
	Assert::same( array(0 => 'South Park'), $input->getSelectedItem() );
	Assert::true( $input->isFilled() );
});



test(function() use ($series) { // empty key
	$form = new Form;
	$input = $form->addMultiSelect('empty', NULL, $series);

	Assert::true( $form->isValid() );
	Assert::same( array(''), $input->getValue() );
	Assert::same( array('' => 'Family Guy'), $input->getSelectedItem() );
	Assert::true( $input->isFilled() );
});



test(function() use ($series) { // missing key
	$form = new Form;
	$input = $form->addMultiSelect('missing', NULL, $series);

	Assert::true( $form->isValid() );
	Assert::same( array(), $input->getValue() );
	Assert::same( array(), $input->getSelectedItem() );
	Assert::false( $input->isFilled() );
});



test(function() use ($series) { // malformed data
	$form = new Form;
	$input = $form->addMultiSelect('malformed', NULL, $series);

	Assert::true( $form->isValid() );
	Assert::same( array(), $input->getValue() );
	Assert::same( array(), $input->getSelectedItem() );
	Assert::false( $input->isFilled() );
});



test(function() use ($series) { // validateLength
	$form = new Form;
	$input = $form->addMultiSelect('multi', NULL, $series);

	Assert::true( $input::validateLength($input, 2) );
	Assert::false( $input::validateLength($input, 3) );
	Assert::false( $input::validateLength($input, array(3, )) );
	Assert::true( $input::validateLength($input, array(0, 3)) );
});



test(function() use ($series) { // validateEqual
	$form = new Form;
	$input = $form->addMultiSelect('multi', NULL, $series);

	Assert::true( $input::validateEqual($input, 'red-dwarf') );
	Assert::false( $input::validateEqual($input, 'unknown') );
	Assert::false( $input::validateEqual($input, array('unknown')) );
	Assert::true( $input::validateEqual($input, array(0)) );
});



test(function() use ($series) { // setValue() and invalid argument
	$form = new Form;
	$input = $form->addMultiSelect('select', NULL, $series);
	$input->setValue(NULL);

	Assert::exception(function() use ($input) {
		$input->setValue('unknown');
	}, 'Nette\InvalidArgumentException', "Values 'unknown' are out of range of current items.");
});
