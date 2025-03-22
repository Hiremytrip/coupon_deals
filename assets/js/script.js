// Copy coupon code to clipboard
function copyToClipboard(text) {
  const textarea = document.createElement("textarea")
  textarea.value = text
  document.body.appendChild(textarea)
  textarea.select()
  document.execCommand("copy")
  document.body.removeChild(textarea)

  // Show copied message
  alert("Coupon code copied to clipboard: " + text)
}

// Initialize tooltips
document.addEventListener("DOMContentLoaded", () => {
  // Ensure Bootstrap is available
  if (typeof bootstrap === "undefined") {
    console.error("Bootstrap is not loaded. Tooltips may not function correctly.")
    return
  }

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Add click event to coupon code elements
  const couponCodes = document.querySelectorAll(".coupon-code")
  couponCodes.forEach((code) => {
    code.addEventListener("click", function () {
      const couponText = this.textContent.trim()
      copyToClipboard(couponText)
    })
  })

  // Add click event to coupon buttons
  const couponButtons = document.querySelectorAll(".get-coupon-btn")
  couponButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const couponId = this.getAttribute("data-coupon-id")
      const couponCode = this.getAttribute("data-coupon-code")
      const storeUrl = this.getAttribute("data-store-url")

      // Increment coupon clicks
      fetch("ajax/increment_click.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "coupon_id=" + couponId,
      })

      // Copy coupon code if available
      if (couponCode) {
        copyToClipboard(couponCode)
      }

      // Redirect to store
      if (storeUrl) {
        window.open(storeUrl, "_blank")
      }
    })
  })
})

