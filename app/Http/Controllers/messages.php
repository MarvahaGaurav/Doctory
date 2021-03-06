<?php

return [
	
	'success' => [
		'login' => 'Login Successfull.',
		'success' => 'Success.',
		'signup' => 'Signup Successfull.',
		'update' => 'Update Successfull.',
		'unsuccess' => 'Unsuccessful.',
		'logout' => 'Logout Successfull.',
		'review_published' => 'review published successfully.',
		'review_un_published' => 'review un published successfully.',
		'appointment_accepted' => 'Appointment accepted Successfully.',
		'appointment_rejected' => 'Appointment rejected Successfully.',
		'password_updated' => 'Password updated Successfully.',
		'appointment_rescheduled' => 'Appointment rescheduled Successfully.',
		'appointment_scheduled' => 'Appointment scheduled Successfully.',
		'NO_DATA_FOUND' => 'NO DATA FOUND',
		'otp_resend' => 'OTP resend successfully.',
		'otp_verified' => 'OTP verified successfully.',
		'email_forget_otp' => 'Code send successfully.',
		'reset_password' => 'Password is successfully reset .Please login again.',
		'mobile_changed' => 'Mobile number successfully changed',
		'complete_profile' => 'Profile is created.',
		'QA_added' => 'Qualification added successfully.',
		'QA_deleted' => 'Qualification deleted successfully.',
		'QA_already_exist' => 'Qualification already exist.',
		'mother_language_added' => 'Mother language added successfully.',
		'mother_language_already_exist' => 'Mother language already exist.',
		'ML_deleted' => 'Mother language deleted.',
		'speciality_added' => 'Speciality added successfully.',
		'SP_deleted' => 'Speciality deleted.',
		'speciality_already_exist' => 'Speciality already exist.',
		'patient_unblocked' => 'Patient unblocked successfully.',
		'patient_blocked' => 'Patient blocked successfully.',
		'docotr_approved' => 'Doctor approved successfully.',
		'speciality_updated' => 'Speciality updated successfully.',
		'qualificationy_updated' => 'Qualification updated successfully.',
		'mother_language_updated' => 'Mother language updated successfully',
		'Admin_profile_updated' => 'Admin profile updated successfully.',
		'Image_uploaded_success' => 'Image uploaded successfully.',
		'Video_uploaded_success' => 'Video uploaded successfully.',
		'Audio_uploaded_success' => 'Audio uploaded successfully.',

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

	'appointment_status' => [
		'PENDING' => 'Pending',
		'ACCEPTED_BY_DOCTOR' => 'Accepted by doctor',
		'REJECTED_BY_DOCTOR' => 'Rejected by doctor',
		'CANCELED_BY_DOCTOR' => 'Canceled by doctor',
		'CANCELED_BY_PATIENT' => 'Canceled by patient',
		'RESCHEDULED_BY_PATIENT' => 'Rescheduled by patient',
		'RESCHEDULED_BY_DOCTOR' => 'Rescheduled by doctor',
		'RESCHEDULED_ACCEPTED_BY_PATIENT' => 'Rescheduled appointment accepted by patient',
		'RESCHEDULED_REJECTED_BY_PATIENT' => 'Rescheduled appointment rejected by patient',
		'RESCHEDULED_ACCEPTED_BY_DOCTOR' => 'Rescheduled appointment accepted by doctor',
		'RESCHEDULED_REJECTED_BY_DOCTOR' => 'Rescheduled appointment rejected by doctor',
		'COMPLETED' => 'Completed',
		'Appointment_Cancelled_By_Patient' => 'Appointment cancelled successfully.',
		'Appointment_Cancelled_By_Doctor' => 'Appointment cancelled successfully.'
	],

	'notification_status_codes' => [
		'Rescheduled_Appointment' => '1',
		'Scheduled_Appointment' => '2',
		'Rescheduled_Appointment_Accepted_By_Patient' => '3',
		'Rescheduled_Appointment_Accepted_By_Doctor' => '4',
		'Rescheduled_Appointment_Rejected_By_Patient' => '5',
		'Rescheduled_Appointment_Rejected_By_Doctor' => '6',
		'Appointment_Rescheduled_By_Patient' => '7',
		'Appointment_Rescheduled_By_Doctor' => '8',
		'Appointment_Accepted_By_Doctor' => '9',
		'Appointment_Rejected_By_Doctor' => '10',
		'Appointment_Cancelled_By_Patient' => '11',
		'Appointment_Cancelled_By_Doctor' => '12',
		'Appointment_Transfered_By_Doctor' => '13'
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

	'QA_exist_under_doctor' => 'Unable to delete.',
	'ML_exist_under_doctor' => 'Unable to delete',
	'SP_exist_under_doctor' => 'Unable to delete',
	'speciality_already_exist' => 'Speciality already exist',
	'qualificationy_already_exist' => 'Qualificationy already exist',
	'mother_language_already_exist' => 'Mother language already exist',
	'invalid_old_password' => 'Old password is incorrect.'
];
