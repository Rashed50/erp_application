export function setToast(type, message) {
  sessionStorage.setItem("toast", JSON.stringify({ type, message }));
}
