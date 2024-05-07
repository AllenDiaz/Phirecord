const Encore = require("@symfony/webpack-encore")

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (! Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev")
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/")

    // public path used by the web server to access the output path
    .setPublicPath("/build")

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry("app", "./resources/js/app.js")
    .addEntry("auth", "./resources/js/auth.js")
    .addEntry("config", "./resources/js/config.js")
    .addEntry("theme", "./resources/js/theme.js")
    .addEntry("main", "./resources/js/main.js")
    .addEntry("auth_admin", "./resources/js/auth_admin.js")
    .addEntry("auth_hospital", "./resources/js/auth_hospital.js")
    .addEntry("auth_patient", "./resources/js/auth_patient.js")
    .addEntry("auth_doctor", "./resources/js/auth_doctor.js")
    .addEntry("nav", "./resources/js/nav.js")
    .addEntry("admin_hospital", "./resources/js/admin/admin_hospital.js")
    .addEntry("approved_hospital", "./resources/js/admin/approved_hospital.js")
    .addEntry("pending_hospital", "./resources/js/admin/pending_hospital.js")
    .addEntry("approved_archive", "./resources/js/admin/approved_archive.js")
    .addEntry("declined_archive", "./resources/js/admin/declined_archive.js")
    .addEntry("assistant_admin", "./resources/js/head_admin/assistant_admin.js")
    .addEntry("hospital_doctor", "./resources/js/hospital/hospital_doctor.js")
    .addEntry("hospital_doctor_pending", "./resources/js/hospital/hospital_doctor_pending.js")
    .addEntry("hospital_patient", "./resources/js/hospital/hospital_patient.js")
    .addEntry("hospital_patient_pending", "./resources/js/hospital/hospital_patient_pending.js")
    .addEntry("patient_accepted_archive", "./resources/js/hospital/patient_accepted_archive.js")
    .addEntry("patient_declined_archive", "./resources/js/hospital/patient_declined_archive.js")
    .addEntry("doctor_accepted_archive", "./resources/js/hospital/doctor_accepted_archive.js")
    .addEntry("doctor_declined_archive", "./resources/js/hospital/doctor_declined_archive.js")
    .addEntry("patient_admission", "./resources/js/admission/patient_admission.js")
    .addEntry("patient_checkup", "./resources/js/checkup/patient_checkup.js")
    .addEntry("patient_medical", "./resources/js/medical/patient_medical.js")
    .addEntry("patient_referral", "./resources/js/referral/patient_referral.js")
    .addEntry("patient_referred", "./resources/js/referral/patient_referred.js")
    .addEntry("doctor_patient_admission", "./resources/js/doctor/doctor_patient_admission.js")
    .addEntry("patient_pending_prescription", "./resources/js/doctor/patient_pending_prescription.js")
    .addEntry("patient_prescription", "./resources/js/doctor/patient_prescription.js")
    .addEntry("patient_patient_admission", "./resources/js/patient/patient_admission.js")
    .addEntry("patient_request_admission", "./resources/js/patient/admission_request.js")
    .addEntry("patient_patient_checkup", "./resources/js/patient/patient_checkupform.js")
    .addEntry("checkupform_request", "./resources/js/patient/checkupform_request.js")
    .addEntry("patient_patient_medical", "./resources/js/patient/patient_medical.js")
    .addEntry("patient_medical_request", "./resources/js/patient/patient_medical_request.js")
    .addEntry("patient_hospital_referral", "./resources/js/referral/patient_hospital_referral.js")
    .addEntry("hospital_requested_admission", "./resources/js/hospital/admission_request.js")
    .addEntry("hospital_requested_checkup", "./resources/js/hospital/checkupform_request.js")
    .addEntry("hospital_requested_medical", "./resources/js/hospital/medical_request.js")
    
    .addEntry("eye", "./resources/js/eye_password.js")

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(! Encore.isProduction())

    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning()

    .configureBabel((config) => {
        config.plugins.push("@babel/plugin-proposal-class-properties")
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage"
        config.corejs      = 3
    })

    .copyFiles({
        from: "./resources/images",
        to: "images/[path][name].[hash:8].[ext]",
        pattern: /\.(png|jpg|jpeg|gif|svg)$/
    })

    // enables Sass/SCSS support
    .enableSassLoader()

module.exports = Encore.getWebpackConfig()
