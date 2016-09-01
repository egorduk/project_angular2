import { EventEmitter, Injectable, Input, Output, Component } from "@angular/core";

@Component({
    selector: 'child-selector',
    templateUrl: 'app/common/service/child.component.html'
})

export class ChildComponent {
@Output() notify: EventEmitter<string> = new EventEmitter<string>();

    onClick() {
        this.notify.emit('Click from nested component');
    }
}