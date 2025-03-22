/**
 * Image Selector Script for CouponDeals Admin
 * This script adds image selection functionality to store and coupon forms
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize image selector buttons
  const imageSelectorButtons = document.querySelectorAll(".image-selector-btn")

  imageSelectorButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault()
      const targetField = this.getAttribute("data-target")
      const imageType = this.getAttribute("data-type")

      // Create and open modal
      openImageSelectorModal(targetField, imageType)
    })
  })

  // Function to open image selector modal
  function openImageSelectorModal(targetField, imageType) {
    // Create modal element
    const modal = document.createElement("div")
    modal.className = "modal fade"
    modal.id = "imageSelectorModal"
    modal.setAttribute("tabindex", "-1")
    modal.setAttribute("aria-labelledby", "imageSelectorModalLabel")
    modal.setAttribute("aria-hidden", "true")

    // Set modal content
    modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageSelectorModalLabel">Select Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading images...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="uploadNewBtn">Upload New Image</button>
                    </div>
                </div>
            </div>
        `

    // Add modal to document
    document.body.appendChild(modal)

    // Initialize Bootstrap modal
    const modalInstance = new bootstrap.Modal(modal)
    modalInstance.show()

    // Load images
    fetchImages(imageType, modal, targetField)

    // Handle "Upload New Image" button
    document.getElementById("uploadNewBtn").addEventListener("click", () => {
      modalInstance.hide()
      window.location.href = "images.php?return=" + encodeURIComponent(window.location.href)
    })

    // Clean up when modal is closed
    modal.addEventListener("hidden.bs.modal", () => {
      document.body.removeChild(modal)
    })
  }

  // Function to fetch images from server
  function fetchImages(imageType, modal, targetField) {
    const folder = imageType === "store" ? "stores" : "coupons"

    // Fetch images using AJAX
    fetch("ajax/get_images.php?type=" + folder)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          displayImages(data.images, modal, targetField, folder)
        } else {
          showError(modal, data.message || "Failed to load images")
        }
      })
      .catch((error) => {
        showError(modal, "Error loading images: " + error.message)
      })
  }

  // Function to display images in modal
  function displayImages(images, modal, targetField, folder) {
    const modalBody = modal.querySelector(".modal-body")

    if (images.length === 0) {
      modalBody.innerHTML = `
                <div class="alert alert-info">
                    No images found. Please upload images first.
                </div>
            `
      return
    }

    // Create image grid
    let html = '<div class="row">'

    images.forEach((image) => {
      html += `
                <div class="col-md-3 col-sm-4 col-6 mb-3">
                    <div class="card h-100 image-select-card" data-image="${image}" data-target="${targetField}">
                        <div class="image-container">
                            <img src="../assets/images/${folder}/${image}" alt="${image}" class="img-fluid">
                        </div>
                        <div class="card-body p-2">
                            <p class="card-text small text-truncate" title="${image}">${image}</p>
                        </div>
                    </div>
                </div>
            `
    })

    html += "</div>"
    modalBody.innerHTML = html

    // Add click event to image cards
    const imageCards = modalBody.querySelectorAll(".image-select-card")
    imageCards.forEach((card) => {
      card.addEventListener("click", function () {
        const imageName = this.getAttribute("data-image")
        const targetFieldId = this.getAttribute("data-target")

        // Update the target input field
        document.getElementById(targetFieldId).value = imageName

        // If there's a preview element, update it too
        const previewElement = document.getElementById(targetFieldId + "_preview")
        if (previewElement) {
          previewElement.src = `../assets/images/${folder}/${imageName}`
          previewElement.style.display = "block"
        }

        // Close the modal
        const modalInstance = bootstrap.Modal.getInstance(modal)
        modalInstance.hide()
      })
    })
  }

  // Function to show error in modal
  function showError(modal, message) {
    const modalBody = modal.querySelector(".modal-body")
    modalBody.innerHTML = `
            <div class="alert alert-danger">
                ${message}
            </div>
        `
  }
})

