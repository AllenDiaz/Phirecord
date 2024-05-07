import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from "sweetalert2";

window.addEventListener("DOMContentLoaded", function () {
  const viewAdminModal = new Modal(document.getElementById("viewAdminModal"));

  const table = new DataTable("#registeredAdminTable", {
    serverSide: true,
    ajax: "/admin/head/register/admin/load",
    orderMulti: false,
    columns: [
      {
        sortable: false,
        data: "profileImage",
      },
      { data: "name" },
      { data: "email" },
      { data: "contactNo" },
      { data: "createdAt" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">
                        <button type="submit" class="btn btn-outline-primary delete-admin-btn" data-id="${row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <button class="ms-2 btn btn-outline-primary view-admin-btn" data-id="${row.id}" data-image="${row.adminIdPicture}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                        <button class="ms-2 btn btn-outline-primary assign-head-btn" data-id="${row.id}">
                           Assign Head
                        </button>
                    </div>
                `,
      },
    ],
  });

  document
    .querySelector("#registeredAdminTable")
    .addEventListener("click", function (event) {
      const viewBtn = event.target.closest(".view-admin-btn");
      const deleteBtn = event.target.closest(".delete-admin-btn");
      const assignBtn = event.target.closest(".assign-head-btn");

      if (viewBtn) {
        const viewId = viewBtn.getAttribute("data-id");
        const viewImage = viewBtn.getAttribute("data-image");
        const viewAdress = viewBtn.getAttribute("data-address");

        openEditCategoryModal(viewAdminModal, viewId, viewImage, viewAdress);
      } else if (deleteBtn) {
        const hospitalId = deleteBtn.getAttribute("data-id");
        Swal.fire({
          title: "Are you sure?",
          text: "The assistant admin will be deleted permanently!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Deleted!",
              text: "Your file has been deleted.",
              icon: "success",
            });
            del(`/admin/head/assistant/delete/${hospitalId}`).then(
              (response) => {
                if (response.ok) {
                  table.draw();
                }
              }
            );
          }
        });
      } else if (assignBtn) {
        const adminId = assignBtn.getAttribute("data-id");
        Swal.fire({
          title:
            "Are you sure you want this Support Admin to be the Head Admin?",
          text: "You will be logged out!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Confirm",
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Note!",
              text: "The new head admin was assigned.",
              icon: "success",
            });
            get(`/admin/head/assistant/assign/${adminId}`).then((response) => {
              if (response.ok) {
                get(`/admin/logout`).then((response) => {
                  if (response.ok) {
                    location.reload();
                  }
                });
              }
            });
          }
        });
      }
    });

  function openEditCategoryModal(modal, id, image, address) {
    const hospitalImage = modal._element.querySelector(".hospital-image");
    const hospitalAddress = modal._element.querySelector("#hospital-address");

    hospitalImage.src = "/img/admin/" + image;
    hospitalAddress.innerHTML = address;

    modal.show();
  }
});
