import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
  const viewHospitalModal = new Modal(
    document.getElementById("viewHospitalModal")
  );

  const table = new DataTable("#approvedHospitalTable", {
    serverSide: true,
    ajax: "/admin/register/hospital/load",
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
                        <button type="submit" class="btn btn-outline-primary delete-hospital-btn" data-id="${row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <button class="ms-2 btn btn-outline-primary view-hospital-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#approvedHospitalTable").addEventListener("click", function (event) {
      const viewBtn = event.target.closest(".view-hospital-btn");
      const deleteBtn = event.target.closest(".delete-hospital-btn");

      if (viewBtn) {
        const viewId = viewBtn.getAttribute("data-id")
        const viewImage = viewBtn.getAttribute("data-image")
        const viewAdress = viewBtn.getAttribute("data-address")
        openEditCategoryModal(viewHospitalModal, viewImage, viewAdress);
      
      } else {
        const hospitalId  = deleteBtn.getAttribute("data-id")
                Swal.fire({
                title: "Are you sure?",
                text: "The Hospital will be remove!",
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
                  get(`/admin/register/archive/${hospitalId}`).then(response =>  {
                    if (response.ok) {
                        table.draw()
                    }
                  })

                }
                })
        }
      })

  function openEditCategoryModal(modal, image, address) {
    const hospitalImage = modal._element.querySelector(".hospital-image");
    const hospitalAddress = modal._element.querySelector("#hospital-address");

    hospitalImage.src = "/img/hospital/" + image;
    hospitalAddress.innerHTML = address;

    modal.show();
  }
});
