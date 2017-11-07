<?php

return [
	
	'success' => [
		'login' => 'Login Successfull.',
		'success' => 'Success.',
		'signup' => 'Signup Successfull.',
		'update' => 'Update Successfull.',
		'unsuccess' => 'Unsuccessful.',
		'logout' => 'Logout Successfull.',
		'request_accepted' => 'request accepted successfully.',
		'request_rejected' => 'request rejected.',
		'review_published' => 'review published successfully.',
		'review_un_published' => 'review un published successfully.',
		'appointment_accepted' => 'Appointment accepted Successfully.',
		'appointment_rejected' => 'Appointment rejected Successfully.',
		'password_updated' => 'Password updated Successfully.',
		'appointment_rescheduled' => 'Appointment rescheduled Successfully.',
		'appointment_scheduled' => 'Appointment scheduled Successfully.',
		'NO_DATA_FOUND' => 'NO DATA FOUND'
	],

	'error' => [
		'insert' => 'Error in record creation.',
		'incorrect_old_password' => 'Old password is incorrect.'
	],


	'statusCode' => [
		'ALREADY_EXIST' => '422',
		'PARAMETER_MISSING' => '422',
		'INVALID_ACCESS_TOKEN' => '401',
		'INVALID_CREDENTIAL' => '403',
		'ACTION_COMPLETE' => '200',
		'CREATE' => '201',
		'NO_DATA_FOUND' => '204',
		'IMAGE_FILE_MISSING' => '422',
		'SHOW_ERROR_MESSAGE' => '400',
		'ERROR_IN_EXECUTION' => '404',
		'BAD_REQUEST' => '500'
	],

	'required' => [
		'accessToken' => 'Access Token Required.',
		'user_id' => 'User id is required.',
		'requestToUserId' => 'RequestToUserId is required',
		'locale' => 'Locale is required',
	],

	'invalid' =>[
		'number' => 'Invalid number.',
		'detail' => 'Invalid details.',
		'request' => 'Invalid request.',
		'credentials' => 'Invalid credentials.',
		'accessToken' => 'Invalid accessToken.',
		'OTP' => 'Invalid OTP.',
		'appointment_date' => 'You can not book appointment for previous days',
		'doctor_not_available_at_this_time_slot' => 'Doctor not available at this time slot',
		'appointment_expired' => 'Appointment Experied.'
	],

	'same' => [
		'same_number' => 'You have entered the same number.',
		'country_code' => 'Same country code as last.'
	],

	'Already_Busy_Time_Slot_With_Other_Patient' => 'Already Busy Time Slot With Other Patient',

	'Patient_Already_Booked_appointment' => 'you have already booked your appointment.',
	'Appointment_already_booked_at_this_time_slot_for_this_patient' => 'Appointment already booked at this time slot for this patient.',


];
