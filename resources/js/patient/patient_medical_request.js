import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
  const viewMedicalModal = new Modal(
    document.getElementById("medicalModal")
  );
  const table = new DataTable("#patientMedicalRequestTable", {
    serverSide: true,
    ajax: "/patient/medicalform/request/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "referenceCode" },
      { data: "requestDate" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">

                        <a href="#">
                        <button class="ms-2 btn btn-outline-primary view-medical-btn" data-id="${row.id}" data-address="${row.patientAddress}" data-name="${row.patient}" data-age="${row.patientAge}" data-gender="${row.patientGender}" data-hospital="${row.hospital}" data-hospitalAddress="${row.hospitalAddress}" data-certificateDate="${row.certificateDate}" data-purpose="${row.purpose}" data-impression="${row.impression}">
                            Details
                        </button>
                        </a>
                         <button class="ms-2 btn btn-outline-primary download-reference-btn" data-id="${row.id}">Print Reference Number</button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patientMedicalRequestTable").addEventListener("click", function (event) {
  const detailBtn = event.target.closest(".view-medical-btn")
  const downloadReference = event.target.closest(".download-reference-btn");

  if(detailBtn) {
       const patientName = detailBtn.getAttribute("data-name");
       const patientAddress = detailBtn.getAttribute("data-address");
       const patientGender = detailBtn.getAttribute("data-gender");
       const patientAge = detailBtn.getAttribute("data-age");
       const hospitalName = detailBtn.getAttribute("data-hospital");
       const hospitalAddress = detailBtn.getAttribute("data-hospitalAddress");
       const patientCertificateDate = detailBtn.getAttribute("data-certificateDate");
       const patientPurpose = detailBtn.getAttribute("data-purpose");
       const patientImpression = detailBtn.getAttribute("data-impression");

        viewMedicalModal._element
                              .querySelector('#patientName')
                              .innerHTML = patientName
        viewMedicalModal._element
                              .querySelector('#patientGender')
                              .innerHTML = patientGender
        viewMedicalModal._element
                              .querySelector('#patientAge')
                              .innerHTML = patientAge
        viewMedicalModal._element
                              .querySelector('#patientAddress')
                              .innerHTML = patientAddress
        viewMedicalModal._element
                              .querySelector('#hospitalName')
                              .innerHTML = hospitalName
        viewMedicalModal._element
                              .querySelector('#hospitalAddress')
                              .innerHTML = hospitalAddress
        viewMedicalModal._element
                              .querySelector('#patientCertificateDate')
                              .innerHTML = patientCertificateDate
        viewMedicalModal._element
                              .querySelector('#patientPurpose')
                              .innerHTML = patientPurpose
        viewMedicalModal._element
                              .querySelector('#patientImpression')
                              .innerHTML = patientImpression

              viewMedicalModal.show()
  } else if(downloadReference) {
     const medicalId = downloadReference.getAttribute("data-id")
      window.open(`/patient/medicalform/${medicalId}/referral`, `_blank`);
  }
    

})

});
