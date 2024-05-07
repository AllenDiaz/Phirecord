import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';
import { data } from "jquery";

window.addEventListener("DOMContentLoaded", function () {
  const viewHospitalModal = new Modal(
    document.getElementById("viewHospitalModal")
  );

  const table = new DataTable("#pendingHospitalTable", {
    serverSide: true,
    ajax: "/admin/register/hospital/pendingload",
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
                    <div class="d-flex gap-2">                  
                        <button type="button" class="btn btn-success accept-hospital-btn" data-id="${row.id}">Accept</button>
                        <button type="button" class="btn btn-danger reject-hospital-btn" data-id="${row.id}">Reject</button>
                        <button class="ms-2 btn btn-outline-primary view-hospital-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#pendingHospitalTable").addEventListener("click", function (event) {
      const viewBtn = event.target.closest(".view-hospital-btn")
      const acceptBtn = event.target.closest('.accept-hospital-btn')
      const rejectBtn = event.target.closest('.reject-hospital-btn')

      if (viewBtn) {
        const viewId = viewBtn.getAttribute("data-id");
        const viewImage = viewBtn.getAttribute("data-image");
        const viewAdress = viewBtn.getAttribute("data-address");
        openEditCategoryModal(viewHospitalModal, viewImage, viewAdress);
      
      } else if (acceptBtn) {
        const hospitalId  = acceptBtn.getAttribute("data-id")
        get(`/admin/register/accept/${hospitalId}`).then(response =>  {
          if(response.ok) {
            table.draw()
              Swal.fire({
              title: "Hospital Approve!",
              text: "Succesfully approved!",
              icon: "success"
              });
          }
        })

      } else if (rejectBtn) {
             const hospitalId  = rejectBtn.getAttribute("data-id")
                Swal.fire({
                title: "Are you sure you declined this hospital?",
                text: "The Hospital will be rejected!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, reject it!"
                }).then((result) => {
                 
              if (result.isConfirmed) {
                    Swal.fire({
                    title: "Note!",
                    text: "Hospital is rejected.",
                    icon: "success"
                    });
                  get(`/admin/declined/archive/${hospitalId}`)
                  .then(response => {
                    if (response.ok) {
                        table.draw()
                    }
                  })

                }
                })
      }
    });

  function openEditCategoryModal(modal, image, address) {
    const hospitalImage = modal._element.querySelector(".hospital-image");
    const hospitalAddress = modal._element.querySelector("#hospital-address");

    hospitalImage.src = "/img/hospital/" + image;
    hospitalAddress.innerHTML = address;

    modal.show();
  }
});
