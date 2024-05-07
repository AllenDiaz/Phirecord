import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
  const viewHospitalModal = new Modal(
    document.getElementById("viewHospitalModal")
  );

  const table = new DataTable("#approvedArchiveHospitalTable", {
    serverSide: true,
    ajax: "/admin/archive/approved/load",
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
                        <button type="submit" class="ms-2 btn btn-outline-primary restore-hospital-btn" data-id="${row.id}">
                            Restore
                        </button>
                        <button class="ms-2 btn btn-outline-primary view-hospital-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#approvedArchiveHospitalTable").addEventListener("click", function (event) {
      const viewBtn = event.target.closest(".view-hospital-btn");
      const deleteBtn = event.target.closest(".delete-hospital-btn");
      const restoreBtn = event.target.closest(".restore-hospital-btn");

      if (viewBtn) {
        const viewId = viewBtn.getAttribute("data-id")
        const viewImage = viewBtn.getAttribute("data-image")
        const viewAdress = viewBtn.getAttribute("data-address")
        openEditCategoryModal(viewHospitalModal, viewImage, viewAdress);
      
      } else if(deleteBtn) {
        const hospitalId  = deleteBtn.getAttribute("data-id")
                Swal.fire({
                title: "Are you sure?",
                text: "The Hospital will be permanently remove!",
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
                  del(`/admin/registered/archive/${hospitalId}`)
                  .then(response => {
                    if (response.ok) {
                        table.draw()
                    }
                  })

                }
                })
        }
         else if(restoreBtn) {
          const hospitalId  = restoreBtn.getAttribute("data-id")
                Swal.fire({
                title: "The hospital will be recover",
                text: "Do you want to recover this hospital?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
                }).then((result) => {
                 
              if (result.isConfirmed) {
                    Swal.fire({
                    title: "The Hospital!",
                    text: "Is succesfully recovered",
                    icon: "success"
                    });
                  get(`/admin/recover/approved/${hospitalId}`)
                  .then(response => {
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
