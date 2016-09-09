import { EventEmitter, Injectable, Input, Output } from "@angular/core";

@Injectable()
export class LoggedService {

    @Input()
    data: boolean = false;

    @Output()
    _emitter: EventEmitter<boolean> = new EventEmitter<boolean>();

    public rxEmitter: any;

    constructor() {
        this.rxEmitter = this._emitter;
    }

    emit(data: any) {
        this.rxEmitter.next(data);
    }
}