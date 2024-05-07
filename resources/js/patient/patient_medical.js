import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
  const viewMedicalModal = new Modal(
    document.getElementById("medicalModal")
  );
  const table = new DataTable("#patientMedicalTable", {
    serverSide: true,
    ajax: "/patient/medicalform/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "certificateDate" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">

                        <a href="#">
                        <button class=" ms-2 btn btn-outline-primary view-medical-btn" data-id="${row.id}" data-address="${row.patientAddress}" data-name="${row.patient}" data-age="${row.patientAge}" data-gender="${row.patientGender}" data-hospital="${row.hospital}" data-hospitalAddress="${row.hospitalAddress}" data-certificateDate="${row.certificateDate}" data-purpose="${row.purpose}" data-impression="${row.impression}">
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

  document.querySelector("#patientMedicalTable").addEventListener("click", function (event) {
  const detailBtn = event.target.closest(".view-medical-btn")
  const requestMedical = event.target.closest(".request-admission-btn");
  const requestedMedical = event.target.closest(".requested-btn");
  const viewPdf = event.target.closest(".view-pdf-btn");

    

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
  } else if(requestedMedical) {
        Swal.fire("You already have requested this form check your medical certificate request and get reference number!");
  } else if (requestMedical) {
      const medicalId = requestMedical.getAttribute("data-id")
          Swal.fire({
            title: "Do you want to request this medical form?",
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
              get(`/patient/medicalform/${medicalId}/requested`)
                .then(response => {
                  if (response.ok) {
                    table.draw()
                  }
                })

            }
          })
  } else if (viewPdf) {
        const medicalForm = viewPdf.getAttribute("data-id");
         window.open(`/patient/medicalCertificate/${medicalForm}/pdf`, `_blank`);
       }

    

})

});
