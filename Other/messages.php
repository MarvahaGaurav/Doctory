<?php

return [
	
	'success' => [
		'payment' => 'Payment Successful.',
		'login' => 'Login Successful.',
		'success' => 'Success.',
		'signup' => 'Signup Successful.',
		'update' => 'Update Successful.',
		'unsuccess' => 'Unsuccessful.',
		'logout' => 'Logout Successful.',
		'review_published' => 'review published successfully.',
		'review_un_published' => 'review un published successfully.',
		'review_deleted' => 'review deleted.',
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

	'notification_messages' => [
		'PENDING' => 'Pending',
		'ACCEPTED_BY_DOCTOR' => 'Your appointment has been accepted.',
		'REJECTED_BY_DOCTOR' => 'Your appointment has been rejected.',
		'CANCELED_BY_DOCTOR' => 'Your appointment has been cancelled.',
		'CANCELED_BY_PATIENT' => 'Your appointment has been cancelled.',
		'RESCHEDULED_BY_PATIENT' => 'You have a new reschedule request for an appointment.',
		'RESCHEDULED_BY_DOCTOR' => 'You have a new reschedule request for an appointment.',
		'RESCHEDULED_ACCEPTED_BY_PATIENT' => 'Reschedule request has been accepted.',
		'RESCHEDULED_REJECTED_BY_PATIENT' => 'Reschedule request has been rejected.',
		'RESCHEDULED_ACCEPTED_BY_DOCTOR' => 'Reschedule request has been accepted.',
		'RESCHEDULED_REJECTED_BY_DOCTOR' => 'Reschedule request has been rejected.',
		'COMPLETED' => 'Completed',
		'Appointment_Cancelled_By_Patient' => 'Appointment cancelled by patient.',
		'Appointment_Cancelled_By_Doctor' => 'Appointment cancelled successfully.',
		'Appointment_Transfered_By_Doctor' => 'Your appointment has been transferred to other doctor.',
		'Scheduled_Appointment' => 'You have a new appointment request.',
		'Patient_Post_Review_To_Doctor' => 'You have received the review on appointment.',
		'Doctor_Aprroved_By_Admin' => 'Your profile have been approved from admin.',
		'Doctor_Have_Appointment_After_Fifteen_Minutes' => 'Your appointment is about to start.',
		'Patient_Have_Appointment_After_Fifteen_Minutes' => 'Your appointment is about to start.',
		'Extand_Chat_Notification' => 'Your appointment time has been extended.',
		'Appointment_Completed' => 'The Doctor has completed your appointment. You can give your review.',
		'Appointment_Started' => 'Your appointment has started.',
		'cancel_the_transfer_request' => 'You have cancel your transfer appointment request. You have already refund into your wallet.',
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
		'Appointment_Transfered_By_Doctor' => '13',
		'Patient_Post_Review_To_Doctor' => '14',
		'Doctor_Aprroved_By_Admin' => '15',
		'Doctor_Have_Appointment_After_Fifteen_Minutes' => '16',
		'Patient_Have_Appointment_After_Fifteen_Minutes' => '17',
		'Extand_Chat_Notification' => '18',
		'Appointment_Completed' => '19',
		'cancel_the_transfer_request' => '20',

	],

	'notification_type' => [
		'Extend_Chat' => '2',
		'Appointment_Completed' => '3',
		'transferAppointmentByDoctor' => '4'
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
		'appointment_expired' => 'Appointment Experied.',
		'invalid_email_match' => "Your entered email dosen't match with your email.",
		'insufficient_amount' => 'insufficient amount in wallet.',
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
	'invalid_old_password' => 'Old password is incorrect.',
	'Wait_For_Approval_From_Admin' =>'Please Wait for approval from admin',

	'Account_blocked_Patient' => 'Account is blocked by admin',

	'invalid_email_dr' => 'There is no such Doctor has registered with us.',
	'invalid_email_pt' => 'There is no such Patient has registered with us.',


];
