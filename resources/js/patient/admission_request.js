import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
    const viewAdmissionModal = new Modal(
    document.getElementById("admissionModal")
  );
  const table = new DataTable("#patientRequestAdmissionTable", {
    serverSide: true,
    ajax: "/patient/admissionform/request/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "referralCode" },
      { data: "requestDate" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">
                        <a href="#">
                        <button class="ms-2 btn btn-outline-primary view-admission-btn" data-id="${row.id}" data-address="${row.patientAddress}" data-name="${row.patient}" data-age="${row.patientAge}" data-gender="${row.patientGender}" 
                        data-symptoms="${row.symptoms}" data-bloodPressure="${row.bloodPressure}" data-temperature="${ row.temperature }" data-weight="${row.weight}" data-respiratoryRate="${ row.respiratoryRate }" data-pulseRate="${row.pulseRate}" data-oxygenSaturation="${row.oxygenSaturation}" data-diagnosis="${row.diagnosis}" data-hospital="${row.hospital}" data-hospitalAddress="${row.hospitalAddress}" >
                            Details
                        </button>
                        </a>
                        <button class="ms-2 btn btn-outline-primary download-referral-btn" data-id="${row.id}">Print Reference Number</button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patientRequestAdmissionTable").addEventListener("click", function (event) {
    const detailBtn = event.target.closest(".view-admission-btn");
    const downloadReferral = event.target.closest(".download-referral-btn");
    

    if(detailBtn) {
        const patientName = detailBtn.getAttribute("data-name");
        const patientAddress = detailBtn.getAttribute("data-address");
        const patientGender = detailBtn.getAttribute("data-gender");
        const patientAge = detailBtn.getAttribute("data-age");
        const patientSymptoms = detailBtn.getAttribute("data-symptoms");
        const patientBloodPressure = detailBtn.getAttribute("data-bloodPressure");
        const patientTemperature = detailBtn.getAttribute("data-temperature");
        const patientWeight = detailBtn.getAttribute("data-weight");
        const patientRespiratoryRate = detailBtn.getAttribute("data-respiratoryRate");
        const patientPulseRate = detailBtn.getAttribute("data-pulseRate");
        const patientOxygenSaturation = detailBtn.getAttribute("data-oxygenSaturation");
        const patientDiagnosis = detailBtn.getAttribute("data-diagnosis");
        const hospitalName = detailBtn.getAttribute("data-hospital");
        const hospitalAddress = detailBtn.getAttribute("data-hospitalAddress");


        openViewAdmissionModal(
          viewAdmissionModal,
          patientName,
          patientAddress,
          patientGender,
          patientAge,
          patientSymptoms,
          patientBloodPressure,
          patientTemperature,
          patientWeight,
          patientRespiratoryRate,
          patientPulseRate,
          patientOxygenSaturation,
          patientDiagnosis,
          hospitalName,
          hospitalAddress,
        );
    } else if(downloadReferral) {
      const admissionId = downloadReferral.getAttribute("data-id")
      window.open(`/patient/admissionform/${admissionId}/referral`, `_blank`);


        }

    })

function openViewAdmissionModal(modal, name, address, gender, age, symptoms, bloodPressure, temperature, weight, respiratoryRate, pulseRate, oxygenSaturation, diagnosis, hName, hAddress) {
  const patientName = modal._element.querySelector("#patientName");
  const patientAddress = modal._element.querySelector("#patientAddress");
  const patientGender = modal._element.querySelector("#patientGender");
  const patientAge = modal._element.querySelector("#patientAge");
  const patientSymptoms = modal._element.querySelector("#patientSymptoms");
  const patientBloodPressure = modal._element.querySelector("#patientBloodPressure");
  const patientTemperature = modal._element.querySelector("#patientTemperature");
  const patientWeight = modal._element.querySelector("#patientWeight");
  const patientRespiratoryRate = modal._element.querySelector("#patientRespiratoryRate");
  const patientPulseRate = modal._element.querySelector("#patientPulseRate");
  const patientOxygenSaturation = modal._element.querySelector("#patientOxygenSaturation");
  const patientDiagnosis = modal._element.querySelector("#patientDiagnosis");
  const hospitalName = modal._element.querySelector("#hospitalName");
  const hospitalAddress = modal._element.querySelector("#hospitalAddress");

  patientName.innerHTML = name;
  patientAddress.innerHTML = address;
  patientGender.innerHTML = gender;
  patientAge.innerHTML = age;
  patientSymptoms.innerHTML = symptoms;
  patientBloodPressure.innerHTML = bloodPressure;
  patientTemperature.innerHTML = temperature;
  patientWeight.innerHTML = weight;
  patientRespiratoryRate.innerHTML = respiratoryRate;
  patientPulseRate.innerHTML = pulseRate;
  patientOxygenSaturation.innerHTML = oxygenSaturation;
  patientDiagnosis.innerHTML = diagnosis;
  hospitalName.innerHTML = hName;
  hospitalAddress.innerHTML = hAddress;

  modal.show();
}

});
