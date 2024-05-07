import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from "sweetalert2";

window.addEventListener("DOMContentLoaded", function () {
  const viewPendingCheckupModal = new Modal(
    document.getElementById("checkupPendingModal")
  );
  const table = new DataTable("#hospitalRequestCheckupTable", {
    serverSide: true,
    ajax: "/hospital/checkupform/request/load",
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
                        <div class="d-flex">
                        <button class="ms-2 btn btn-outline-primary view-request-btn" data-id="${row.id}" >
                            Get Request
                        </button>
             
                          <button class="ms-2 btn btn-outline-success done-request-btn" data-id="${row.id}" data-request="${row.requestId}">
                            Mark as Done
                        </button>
                    </div>
        
                    </div>
                `,
      },
    ],
  });

  document
    .querySelector("#hospitalRequestCheckupTable")
    .addEventListener("click", function (event) {
        const requestBtn = event.target.closest(".view-request-btn");
        const doneBtn = event.target.closest(".done-request-btn");


        if(requestBtn) {
        const checkupId = requestBtn.getAttribute("data-id");
         window.open(`/hospital/checkupform/pdf/${checkupId}`, `_blank`);
       
        } else if(doneBtn) {
          const checkupId = doneBtn.getAttribute("data-id");
          const requestId = doneBtn.getAttribute("data-request");

            Swal.fire({
          title: "Mark this request as completed?",
          text: "The request will be removed!",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Completed",
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Completed!",
              text: "Admission Request is completed.",
              icon: "success",
            });
            get(`/hospital/checkupform/${checkupId}/request/${requestId}`).then((response) => {
              if (response.ok) {
                table.draw();
              }
            });
          }
        });

        
        }
    });
});
