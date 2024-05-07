import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
  const viewDoctorModal = new Modal(
    document.getElementById("viewDoctorModal")
  );

  const table = new DataTable("#doctorDeclinedArchiveTable", {
    serverSide: true,
    ajax: "/hospital/doctor/declined/archive/load",
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
                        <button type="submit" class="btn btn-outline-primary delete-doctor-btn" data-id="${row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <button type="submit" class="ms-2 btn btn-outline-primary restore-doctor-btn" data-id="${row.id}">
                            Restore
                        </button>
                        <button class="ms-2 btn btn-outline-primary view-doctor-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#doctorDeclinedArchiveTable").addEventListener("click", function (event) {
    const viewBtn = event.target.closest(".view-doctor-btn");
    const deleteBtn = event.target.closest(".delete-doctor-btn");
    const restoreBtn = event.target.closest(".restore-doctor-btn");

    if (viewBtn) {
      const viewId = viewBtn.getAttribute("data-id")
      const viewImage = viewBtn.getAttribute("data-image")
      const viewAdress = viewBtn.getAttribute("data-address")
      openViewDoctorModal(viewDoctorModal, viewImage, viewAdress);

    } else if (deleteBtn) {
      const doctorId = deleteBtn.getAttribute("data-id")
      Swal.fire({
        title: "Are you sure?",
        text: "The Doctor will be permanently remove!",
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
          del(`/hospital/doctor/accepted/archive/${doctorId}`)
            .then(response => {
              if (response.ok) {
                table.draw()
              }
            })

        }
      })
    }
    else if (restoreBtn) {
      const patientId = restoreBtn.getAttribute("data-id")
      Swal.fire({
        title: "The Doctor will be recover",
        text: "Do you want to recover this Doctor?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes"
      }).then((result) => {

        if (result.isConfirmed) {
          Swal.fire({
            title: "The Doctor!",
            text: "Is succesfully recovered",
            icon: "success"
          });
          get(`/hospital/doctor/recover/accepted/${patientId}`)
            .then(response => {
              if (response.ok) {
                table.draw()
              }
            })

        }
      })
    }
  })

  function openViewDoctorModal(modal, image, address) {
      const doctorIdImage = modal._element.querySelector('.doctor-id-image')
      const doctorAddress = modal._element.querySelector('#doctor-address')

      doctorIdImage.src = "/img/doctor/" + image
      doctorAddress.innerHTML = address
    modal.show()
  }
});
