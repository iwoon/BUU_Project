<?php

class Welcome extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		// used in the view
		$this->load->helper('url');		
	}
	
	function index()
	{
		$this->load->library('form');

		// define radiobuttons, checkboxes and select options
		
		$radios[] = array('m', 'male');
		$radios[] = array('f', 'female');

		$checks[] = array('1|c1', 'One', array('style=font-weight:bold'));
		$checks[] = array('2|c2', 'Two');
		$checks[] = array('3|c3', 'Three');
		$checks[] = array('4|c4', 'Four');
		$checks[] = array('5|c5', 'Five');
		
		$single = array(
			'one'	=> 'One Item',
			'two'	=> 'Two Items',
			'three' => 'Three Items',
			'four' 	=> 'Four Items'
		);

		$multiple = array(
			'Group A' => array(
				'1'	=> 'One',
				'2'	=> 'Two',
				'3' => 'Three'
			),
			'Group B' => array(
				'4' => 'Four',
				'5' => 'Five'
			)
		);		

		$this->form

		// if the form is submitted to the same url this can be omitted
		->open('welcome')
				
		/*
		 * Please note that the following example does not intend to be complete.
		 * In a real world form you would add many more rules to validate user input.
		 */

		->fieldset('Some Inputs')
		
		// notice how the max_length[] rule automatically transforms into an attribute
		->text('name', 'Your Name', 'required|max_length[40]')
		
		// since a 'required' rule is set, the 'Password' label will automatically
		// receive a required class and an asterisk (see config file)
		->password('pass', 'Password')
		
		// cols help you to easily create floating elements
		->col(150, 'left', 'right')
		
		// since the 'zip' label cannot be placed in the default label position
		// we have to create individual labels in front of them
		->label('City', 'city', 'class=left combine')
		->label('/Zip', 'zip', 'class=left combine, style=padding-right:10px')
		
		// this col creates a column with an automatic width
		->col()
		
		// now we will create both the 'city' and the 'zip' element
		->text('city|city', '', '', '', 'style=width:108px,element_suffix=""')
				
		// let's add the 'zip' field and use a custom validation function
		->text('zip|zip', '', 'exact_length[5]|is_natural|callback_valid_zip', '', 'style=width:60px')
		
		// and add some margin-left to the last_accessed element ('zip')
		->margin(10)		

		/*
		 * NOTE: error_message() will overwrite all 'per rule' errors. This way you can easily set a combined error like:
		 *
		 * 'The Zip must be a 5-digit number between 00100 and 99999' instead of:
		 *
		 * Error 1: 'The field Zip must be 5 characters in length'
	 	 * Error 2: 'The field Zip must be a natural number'
		 * Error 3: 'The field Zip is not valid' -> from custom callback function
		 *
		 * oh and by the way, the last_accessed element is still 'zip'
		 */		
		->set_error('The Zip must be a 5-digit number between 00100 and 99999.')
		
		// and close the the columns
		->col(0)
		
		// let's add another fieldset
		->fieldset('A File Upload and Textarea')
		
		// this will add a full featured upload input
		// the best way to add the upload specific attributes is in the config file
		->upload('file', 'Upload Image')
		
		// this textarea has some default text and attributes for both the element and its label
		// to include attributes for both the element and its label you need to include them as
		// an array with two values like: array('class=for_element', 'class=for_label')
		->textarea('notes', 'Notes', 'trim', 'This textarea has some default text and both the element and its label are styled. Note how the label is underlined and the textarea has some light yellow background color.', array('style=background:#ffffee', 'style=text-decoration:underline'))

		// margin can also be used to other directions
		// and notice how this margin is using a specific element name ('notes')
		// instead of the last_access method (not really necessary here, just for example)
		->margin('notes', 4, 'top')
		
		->fieldset('Checkboxes, Radiobuttons and Selects')
		
		// look at the source and see how for each radio button and its label an id is being generated
		->radiogroup('gender', $radios, 'Your gender', 'm', 'required')
		
		// add a double break with 'clear: both' after the floating radio buttons
		->space(TRUE)
		
		// indent the following elements by 150px since they don't have a label
		->indent(150)
		
		// add a group of checkboxes to the form, set some of the boxes as checked
		->checkgroup('checks', $checks, '', '2,3,4', 'required', '')
		
		// add a double break with 'clear: both'
		->space(TRUE)
		
		// notice how the checkgroup above doesn't have a 'label', but will throw an error message if none of the boxes are checked?
		// that's because you can assign error messages to any element, even if they don't have a label
		->set_error('checks', 'Please check at least one of the checkboxes.')

		// add a single checkbox
		// the label attribute position = 'before' places the label before the checkbox
		// notice how the 'position' attribute for the label must be included as an array (vs. string for the element's attributes)
		->checkbox('privacy', 'yes', 'I agree to the <a href="#">privacy policy</a>', FALSE, 'required', array('position=before'))
		->space()
		
		/*
		 * error_label() lets you change the label used with the error message, so instead of:
		 * 'The I agree to the privacy policy field is required' you will see:
		 * 'The Privacy Policy field is required'
		 */
		->set_error_label('privacy', 'Privacy Policy')
		
		// reset the indentation to 0px
		->indent(0)
		
		// add a single select field
		->select('single', $single, 'Select one', '2', '', 'size=1,ondblclick=test')
		
		// add another break
		->br()
		
		// add a multiple select field
		// notice how this will automatically become a multiple select since more than one default values are set
		->select('multiple', $multiple, 'Select multiple', '2, 4', '', 'style=width:150px')
		->br()

		// require a recaptcha to be filled out		
		// ->recaptcha('Are you human?')

		->indent(150)
		
		// add a standard submit button ('Submit')
		->submit('Submit')
		
		// this is a nice way to reset a form including resetting all error messages
		// notice how the onclick is enclosed by double quotes in order to use single quotes in the script		
		->reset('Reset', 'reset', "onclick=document.location.href='welcome'")
		
		// add some 'margin-left' to the reset button 
		->margin(10)
		
		// calls a model after the form was submit and validated
		// to run some database queries or do some other stuff
		->model('example', 'do_stuff');

		// get the form output and write it to the $data array
		// this method automatically validates the form data
		$data['form'] = $this->form->get();

		if ($this->form->valid)
		{
			// get the validated and xss cleaned (TRUE) post data as an array
			$post = $this->form->get_post(TRUE);
		
			// do stuff with post vars
			
			// redirect to success page
			redirect('welcome/success');
		}
		else
		{
			// write the errors to the $data array
			// NOTE: this must be done after $form->get()
			$data['errors'] = $this->form->errors;

			// the following variable can be used to get all errors in an associative array
			// might be useful when used with json_encode and AJAX functions:

			// $this->form->error_array;
		}

		$this->load->view("welcome", $data);
	}

	function success()
	{
		$this->load->view("success");
	}
	
	/**
	 * Form Validation Callback Function
	 */
	function valid_zip($zip) 
	{
		// this is just a sample on using custom callbacks
		if ($zip && $zip < 100)
		{ 
			$this->form_validation->set_message('valid_zip', 'The %s is not valid');
			return FALSE;
		}
		
		return TRUE;
	}
}

/* End of file welcome.php */
/* Location: /application/controllers/welcome.php */