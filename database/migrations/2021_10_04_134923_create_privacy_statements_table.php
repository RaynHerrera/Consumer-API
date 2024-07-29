<?php

use App\Traits\ColumnMigrationFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivacyStatementsTable extends Migration
{
    use ColumnMigrationFields;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_privacystatement', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean("account_balance")->nullable();
            $table->boolean("address")->nullable();
            $table->boolean("advertiser_id")->nullable();
            $table->boolean("age")->nullable();
            $table->boolean("agent_number")->nullable();
            $table->boolean("attorneys_name")->nullable();
            $table->boolean("biometric_data")->nullable();
            $table->boolean("birth_certificate")->nullable();
            $table->boolean("browsing_history")->nullable();
            $table->boolean("claim_loss_date")->nullable();
            $table->boolean("claim_loss_type")->nullable();
            $table->boolean("claim_number")->nullable();
            $table->boolean("claim_peril")->nullable();
            $table->boolean("claim_status")->nullable();
            $table->boolean("consumer_spending_habits")->nullable();
            $table->boolean("cookie")->nullable();
            $table->boolean("credit_or_debit_card_account_number")->nullable();
            $table->boolean("credit_or_debit_card_expiration_date")->nullable();
            $table->boolean("credit_or_debit_card_security_code")->nullable();
            $table->boolean("credit_score")->nullable();
            $table->boolean("criminal_conviction")->nullable();
            $table->boolean("customer_number")->nullable();
            $table->boolean("customer_spending_habits")->nullable();
            $table->boolean("customers_income")->nullable();
            $table->boolean("date_of_birth")->nullable();
            $table->boolean("device_id")->nullable();
            $table->boolean("dna_profile")->comment("deoxyribonucleic acid profile")->nullable();
            $table->boolean("driver_authorization_card_number")->nullable();
            $table->boolean("drivers_license_number")->nullable();
            $table->boolean("dwelling_estimated_home_value")->nullable();
            $table->boolean("dwelling_mortgage_amount")->nullable();
            $table->boolean("dwelling_property_index_number_pin")->nullable();
            $table->boolean("dwelling_purchase_price_amount")->nullable();
            $table->boolean("dwelling_type")->nullable();
            $table->boolean("education")->nullable();
            $table->boolean("emaile_address")->nullable();
            $table->boolean("employee_identification_number")->nullable();
            $table->boolean("employee_identification_number_includeing_social_security_numbe")->nullable();
            $table->boolean("employee_job_title")->nullable();
            $table->boolean("employee_pay_grade")->nullable();
            $table->boolean("employee_position_id")->nullable();
            $table->boolean("employee_salary")->nullable();
            $table->boolean("employee_service_date")->nullable();
            $table->boolean("employee_type")->nullable();
            $table->boolean("employee_tax_identification_number")->nullable();
            $table->boolean("employee_status")->nullable();
            $table->boolean("estimated_household_income_range")->nullable();
            $table->boolean("ethnic_origin")->nullable();
            $table->boolean("fax_number")->nullable();
            $table->boolean("financial_account_number")->nullable();
            $table->boolean("financial_account_security_code")->nullable();
            $table->boolean("gender")->nullable();
            $table->boolean("geo_location")->comment("GPS")->nullable();
            $table->boolean("health_insurance_claim_number")->comment("HICN")->nullable();
            $table->boolean("health_insurance_identification_number")->nullable();
            $table->boolean("health_insurance_participant_id")->nullable();
            $table->boolean("health_insurance_plan_id")->nullable();
            $table->boolean("health_insurance_plan_provider")->nullable();
            $table->boolean("health_insurance_plan_type")->nullable();
            $table->boolean("health_related_payment_history")->nullable();
            $table->boolean("height")->nullable();
            $table->boolean("inferences")->nullable();
            $table->boolean("marital_status")->nullable();
            $table->boolean("marriage_certificate")->nullable();
            $table->boolean("medical_diagnosis_or_treatment")->nullable();
            $table->boolean("medical_history")->nullable();
            $table->boolean("medical_identification_number")->nullable();
            $table->boolean("medical_record_number")->nullable();
            $table->boolean("metal_health")->nullable();
            $table->boolean("military_id_number")->nullable();
            $table->boolean("mothers_maiden_name")->nullable();
            $table->boolean("name")->nullable();
            $table->boolean("names_of_employers")->nullable();
            $table->boolean("names_of_relatives")->nullable();
            $table->boolean("number_of_dependents")->nullable();
            $table->boolean("occupation")->nullable();
            $table->boolean("other_government_issued_identification_number")->nullable();
            $table->boolean("passport_number")->nullable();
            $table->boolean("personal_internet_protocal_address")->nullable();
            $table->boolean("personal_web_url")->nullable();
            $table->boolean("photographic_or_video_images")->nullable();
            $table->boolean("physicians_name")->nullable();
            $table->boolean("plan_beneficiary_number")->nullable();
            $table->boolean("policy_coverage_and_limit")->nullable();
            $table->boolean("policy_effective_date")->nullable();
            $table->boolean("policy_expiration_date")->nullable();
            $table->boolean("policy_number")->nullable();
            $table->boolean("policy_status")->nullable();
            $table->boolean("policy_type")->nullable();
            $table->boolean("political_party_affiliation")->nullable();
            $table->boolean("prior_insurance_information")->nullable();
            $table->boolean("professional_certificate_License_number")->nullable();
            $table->boolean("race")->nullable();
            $table->boolean("religious_affiliation")->nullable();
            $table->boolean("search_history")->nullable();
            $table->boolean("sexual_behavPhysicians_nameior_lifestyle")->nullable();
            $table->boolean("signature_digital_or_electronic")->nullable();
            $table->boolean("signature_written")->nullable();
            $table->boolean("smoker_status")->nullable();
            $table->boolean("social_security_number")->nullable();
            $table->boolean("state_identification_card_number")->nullable();
            $table->boolean("student_identification_number")->nullable();
            $table->boolean("taxpayer_identification_number")->nullable();
            $table->boolean("telephone_number")->nullable();
            $table->boolean("thermal_or_olfactory_information")->nullable();
            $table->boolean("trade_union_membership")->nullable();
            $table->boolean("tribal_identification_number")->nullable();
            $table->boolean("user_alias")->nullable();
            $table->boolean("user_name_id")->nullable();
            $table->boolean("user_name_id_and_password")->nullable();
            $table->boolean("vehicle_identification_number")->nullable();
            $table->boolean("vehicle_license_number")->nullable();
            $table->boolean("web_beacon")->nullable();
            $table->boolean("weight")->nullable();
            $table->text("weird_things",2500)->comment("Enterprise Collects from you")->nullable();
            $table->text("how_enterprises_collect_your_information",2500)->nullable();
            $table->text("when_enterprises_collects_your_information",2500)->nullable();
            $table->text("ways_enterprise_uses_your_information",2500)->nullable();
            $table->text("who_else_sees_and_uses_your_information",2500)->nullable();
            $table->text("california_consumer_request_information_link",2500)->nullable();
            $table->text("alifornia_consumer_request_information_phone_number",2500)->nullable();
            $table->text("california_consumer_request_information_other",2500)->nullable();
            $table->text("for_Non_CA_citizen_action_rights_other",2500)->nullable();
            $table->text("for_Non_CA_citizen_action_rights_phone_number",2500)->nullable();
            $table->text("for_Non_CA_citizen_action_rights_link",2500)->nullable();
            $table->text("in_compliance_with_CCPA_existance",2500)->nullable();
            $table->text("in_compliance_with_CCPA_statement",2500)->nullable();
            $table->text("in_compliance_for_GDPR_existance",2500)->nullable();
            $table->text("in_compliance_for_GDPR_statement",2500)->nullable();
            $table->timestamp("elroi_internal_date")->nullable();
            $table->date("qa_1st")->nullable();
            $table->date("qa_2nd")->nullable();
            $table->integer("company")->nullable();
            $table->integer("elroi_internal_author")->nullable();
            $this->AddCommonFields($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_privacystatement');
    }
}
