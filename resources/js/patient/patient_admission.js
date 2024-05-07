import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
    const viewAdmissionModal = new Modal(
    document.getElementById("admissionModal")
  );
  const table = new DataTable("#patientAdmissionTable", {
    serverSide: true,
    ajax: "/patient/admissionform/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "admissionDate" },
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
                        <a href="#">
                          <button class="ms-2 btn btn-outline-primary view-pdf-btn" data-id="${row.id}">PDF</button>
                        </a>
                        
                        ${row.request ? `<a><button class="ms-2 btn btn-success requested-btn" ><i class="mr-5 bi bi-check-square-fill"></i>&nbsp;Requested</button></a>` : `<a><button class="ms-2 btn btn-outline-primary request-admission-btn" data-id=" ${row.id} ">Request</button></a>` 

                        }
                    
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patientAdmissionTable").addEventListener("click", function (event) {
    const detailBtn = event.target.closest(".view-admission-btn");
    const requestAdmission = event.target.closest(".request-admission-btn");
    const requestedAdmission = event.target.closest(".requested-btn");
    const viewPdf = event.target.closest(".view-pdf-btn");
    

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
    } else if(requestAdmission) {
      const admissionId = requestAdmission.getAttribute("data-id")
          Swal.fire({
            title: "Do you want to request this admission form?",
            text: "confirm if yes",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm"
          }).then((result) => {

            if (result.isConfirmed) {
              Swal.fire({
                title: "Success!",
                text: "The form succesfully requested.",
                icon: "success"
              });
              get(`/patient/admissionform/${admissionId}/requested`)
                .then(response => {
                  if (response.ok) {
                    table.draw()
                  }
                })

            }
          })
        } else if(requestedAdmission) {
           Swal.fire("You already have requested this form check your admission request and get reference number!");
        } else if (viewPdf) {
        const admissionId = viewPdf.getAttribute("data-id");
         window.open(`/patient/admissionform/${admissionId}/pdf`, `_blank`);
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
