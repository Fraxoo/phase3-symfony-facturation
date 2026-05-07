import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['dialog'];

  open() {
    if (!this.hasDialogTarget) return;

    this.dialogTarget.showModal();
  }

  close() {
    if (!this.hasDialogTarget) return;

    this.dialogTarget.close();
  }

  backdropClose(event) {
    if (!this.hasDialogTarget) return;

    if (event.target === this.dialogTarget) {
      this.dialogTarget.close();
    }
  }
}
