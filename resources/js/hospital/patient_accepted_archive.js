import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
  const viewPatientModal = new Modal(
    document.getElementById("viewPatientModal")
  );

  const table = new DataTable("#patientAcceptedArchiveTable", {
    serverSide: true,
    ajax: "/hospital/patient/accepted/archive/load",
    orderMulti: false,
    columns: [
      {
        sortable: false,
        data: "profileImage",
      },
      { data: "name" },
      { data: "email" },
      { data: "contactNo" },
      { data: "approveAt" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">
                        <button type="submit" class="btn btn-outline-primary delete-patient-btn" data-id="${row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <button type="submit" class="ms-2 btn btn-outline-primary restore-patient-btn" data-id="${row.id}">
                            Restore
                        </button>
                        <button class="ms-2 btn btn-outline-primary view-patient-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patientAcceptedArchiveTable").addEventListener("click", function (event) {
      const viewBtn = event.target.closest(".view-patient-btn");
      const deleteBtn = event.target.closest(".delete-patient-btn");
      const restoreBtn = event.target.closest(".restore-patient-btn");

      if (viewBtn) {
        const viewId = viewBtn.getAttribute("data-id")
        const viewImage = viewBtn.getAttribute("data-image")
        const viewAdress = viewBtn.getAttribute("data-address")
        openViewPatientModal(viewPatientModal, viewImage, viewAdress);
      
      } else if(deleteBtn) {
        const patientId  = deleteBtn.getAttribute("data-id")
                Swal.fire({
                title: "Are you sure?",
                text: "The Patient will be permanently remove!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                 
              if (result.isConfirmed) {
                    Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                    });
                  del(`/hospital/patient/accepted/archive/${patientId}`)
                  .then(response => {
                    if (response.ok) {
                        table.draw()
                    }
                  })

                }
                })
        }
         else if(restoreBtn) {
          const patientId  = restoreBtn.getAttribute("data-id")
                Swal.fire({
                title: "The patient will be recover",
                text: "Do you want to recover this patient?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
                }).then((result) => {
                 
              if (result.isConfirmed) {
                    Swal.fire({
                    title: "The Patient!",
                    text: "Is succesfully recovered",
                    icon: "success"
                    });
                  get(`/hospital/patient/recover/accepted/${patientId}`)
                  .then(response => {
                    if (response.ok) {
                        table.draw()
                    }
                  })

                }
                })
        }
      })

  function openViewPatientModal(modal, image, address) {
    const patientImage = modal._element.querySelector('.patient-image')
    const patientAddress = modal._element.querySelector('#patient-address')

    patientImage.src = "/img/patient/" + image
    patientAddress.innerHTML = address

    modal.show()
  }
});
