<?php

return [
	
	'success' => [
		'login' => 'تم تسجيل الدخول بنجاح',
		'success' => 'نجاح',
		'signup' => 'الاشتراك بنجاح',
		'update' => 'تم التحديث بنجاح',
		'unsuccess' => 'غير ناجح',
		'logout' => 'تم الخروج بنجاح',
		'review_published' => 'تم نشر المراجعة بنجاح',
		'review_un_published' => 'مراجعة غير المنشورة بنجاح',
		'appointment_accepted' => 'تم قبول الموعد بنجاح',
		'appointment_rejected' => 'تم رفض التعيين بنجاح',
		'password_updated' => 'تم تحديث كلمة السر بنجاح',
		'appointment_rescheduled' => 'تم إعادة جدولة الموعد بنجاح',
		'appointment_scheduled' => 'تم تعيين الموعد بنجاح',
		'NO_DATA_FOUND' => 'لاتوجد بيانات',
		'otp_resend' => 'إعادة إرسال أوتب بنجاح.',
		'otp_verified' => 'تم التحقق من مكتب المدعي العام بنجاح.',
		'email_forget_otp' => 'إرسال رمز بنجاح.',
		'reset_password' => 'تم إعادة تعيين كلمة المرور بنجاح. الرجاء تسجيل الدخول مرة أخرى.',
		'mobile_changed' => 'تم تغيير رقم الجوال بنجاح',
		'complete_profile' => 'تم إنشاء الملف الشخصي',
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
		'insert' => 'حدث خطأ في إنشاء السجل',
		'incorrect_old_password' => 'كلمة سر قديمة ليست صحيحة'
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
		'accessToken' => 'رمز الوصول المطلوب.',
		'user_id' => 'مطلوب معرف المستخدم.',
		'requestToUserId' => 'مطلوب ريكتوسريد',
	],

	'invalid' =>[
		'number' => 'رقم غير صالح',
		'detail' => 'تفاصيل غير صالحة',
		'request' => 'طلب غير صالح',
		'credentials' => 'بيانات الاعتماد غير صالحة',
		'accessToken' => 'تصريح الدخول غير صالح',
		'OTP' => 'مكتب المدعي العام غير صالح',
		'appointment_date' => 'لا يمكنك حجز موعد للأيام السابقة',
		'doctor_not_available_at_this_time_slot' => 'الطبيب غير متوفر في هذه الفتحة الزمنية',
		'appointment_expired' => 'انتهت صلاحية التعيين',
		'invalid_email_match' => "Your entered email dosen't match with your email."
	],

	'same' => [
		'same_number' => 'لقد أدخلت الرقم نفسه',
		'country_code' => 'نفس رمز البلد كآخر'
	],

	'Already_Busy_Time_Slot_With_Other_Patient' => 'بالفعل فتحة الوقت مشغول مع مريض آخر',

	'Patient_Already_Booked_appointment' => 'لقد حجزت مسبقا موعدك',
	'Appointment_already_booked_at_this_time_slot_for_this_patient' => 'حجز حجز بالفعل في هذا الوقت فتحة لهذا المريض',



];
