function vmLinkHandler() {
  var vmvid = this.dataset.video;
  console.log(vmvid);
  window._vm.navigate(vmvid);
}

document.querySelectorAll(".vm-link").forEach((vmLink) => {
  vmLink.addEventListener("click", vmLinkHandler);
})