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
		'password_updated' => 'تم تغيير كلمة المرور بنجاح.',
		'appointment_rescheduled' => 'تمت إعادة حجز الموعد بنجاح',
		'appointment_scheduled' => 'تم حجز الموعد بنجاح.',
		'NO_DATA_FOUND' => 'NO DATA FOUND',
		'otp_resend' => 'OTP resend successfully.',
		'otp_verified' => 'OTP verified successfully.',
		'email_forget_otp' => 'Code send successfully.',
		'reset_password' => 'تمت إعادة ضبط كلمة المرور بنجاح, يرجى تسجيل الدخول مرة أخرى.',
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
		'incorrect_old_password' => 'كلمة المرور القديمة غير صحيحة'
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
		'Appointment_Cancelled_By_Patient' => 'تم إالغاء الموعد بنجاح.',
		'Appointment_Cancelled_By_Doctor' => 'تم إالغاء الموعد بنجاح.'
	],

	'notification_messages' => [
		'PENDING' => 'Pending',
		'ACCEPTED_BY_DOCTOR' => 'تم قبول موعدك',
		'REJECTED_BY_DOCTOR' => 'تم رفض موعدك',
		'CANCELED_BY_DOCTOR' => 'تم إلغاء موعدك.',
		'CANCELED_BY_PATIENT' => 'تم إلغاء موعدك.',
		'RESCHEDULED_BY_PATIENT' => 'تم إلغاء موعدكلديك طلب إعادة جدولة موعد جديد.',
		'RESCHEDULED_BY_DOCTOR' => 'تم إلغاء موعدكلديك طلب إعادة جدولة موعد جديد.',
		'RESCHEDULED_ACCEPTED_BY_PATIENT' => 'تم رفض طلب إعادة الجدولةتم قبول طلب إعادة الجدولة.',
		'RESCHEDULED_REJECTED_BY_PATIENT' => 'تم رفض طلب إعادة الجدولة.',
		'RESCHEDULED_ACCEPTED_BY_DOCTOR' => 'تم رفض طلب إعادة الجدولةتم قبول طلب إعادة الجدولة.',
		'RESCHEDULED_REJECTED_BY_DOCTOR' => 'تم رفض طلب إعادة الجدولة.',
		'COMPLETED' => 'Completed',
		'Appointment_Cancelled_By_Patient' => 'المريض قام بإلغاء الموعد.',
		'Appointment_Cancelled_By_Doctor' => 'تم إالغاء الموعد بنجاح.',
		'Appointment_Transfered_By_Doctor' => 'تم تحويل موعدك إلى طبيب آخر.',
		'Scheduled_Appointment' => 'لديك طلب موعد جديد.',
		'Patient_Post_Review_To_Doctor' => 'لقد حصلت على تقييم لموعد.',
		'Doctor_Aprroved_By_Admin' => 'قامت الإدارة بالموافقة على ملفك.',
		'Doctor_Have_Appointment_After_Fifteen_Minutes' => 'موعدك على وشك البدء.',
		'Patient_Have_Appointment_After_Fifteen_Minutes' => 'موعدك على وشك البدء.',
		'Extand_Chat_Notification' => 'تم تمديد موعدك.',
		'Appointment_Completed' => 'لقد أكمل الطبيب موعدك. يمكنك تقديم رأيك..',
		'Appointment_Started' => 'موعدك بدأ.',
		'cancel_the_transfer_request' => 'تم إالغاء طلب تحويل الموعد, وتم تحويل المبلغ إلى محفظتك.',
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
		'detail' => 'تفاصيل غير صحيحة.',
		'request' => 'Invalid request.',
		'credentials' => 'Invalid credentials.',
		'accessToken' => 'Invalid accessToken.',
		'OTP' => 'Invalid OTP.',
		'appointment_date' => 'You can not book appointment for previous days',
		'doctor_not_available_at_this_time_slot' => 'Doctor not available at this time slot',
		'appointment_expired' => 'انتهت مدة الموعد.',
		'invalid_email_match' => "Your entered email dosen't match with your email.",
		'insufficient_amount' => 'insufficient amount in wallet.',
	],

	'same' => [
		'same_number' => 'You have entered the same number.',
		'country_code' => 'Same country code as last.'
	],

	'Already_Busy_Time_Slot_With_Other_Patient' => 'Already Busy Time Slot With Other Patient',

	'Patient_Already_Booked_appointment' => 'you have already booked your appointment.',
	'Appointment_already_booked_at_this_time_slot_for_this_patient' => 'يوجد موعد محجوز لهذا المريض في هذا الوقت',

	'QA_exist_under_doctor' => 'Unable to delete.',
	'ML_exist_under_doctor' => 'Unable to delete',
	'SP_exist_under_doctor' => 'Unable to delete',
	'speciality_already_exist' => 'Speciality already exist',
	'qualificationy_already_exist' => 'Qualificationy already exist',
	'mother_language_already_exist' => 'Mother language already exist',
	'invalid_old_password' => 'كلمة المرور القديمة غير صحيحة',
	'Wait_For_Approval_From_Admin' =>'Please Wait for approval from admin',

	'Account_blocked_Patient' => 'قامت الإدارة بحظر الحساب',

	'invalid_email_dr' => 'There is no such Doctor has registered with us.',
	'invalid_email_pt' => 'There is no such Patient has registered with us.',


];
