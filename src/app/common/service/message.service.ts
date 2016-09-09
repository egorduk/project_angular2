import { EventEmitter, Injectable, Output } from "@angular/core";

@Injectable()
export class MessageService {

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