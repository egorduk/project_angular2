import { EventEmitter, Injectable, Input, Output } from "@angular/core";

@Injectable()
export class MessageService {

    @Input()
    data: boolean = false;

    @Output()
    _emitter: EventEmitter<boolean> = new EventEmitter<boolean>();

    public rxEmitter: any;
    public input: any;
    public indata: any;
    //private _emitter: EventEmitter<string> = new EventEmitter<string>();

    constructor() {
        //console.log('init');
        this.rxEmitter = this._emitter;
        //this.input = this.data;
        //console.log('this.input', this.input);

        this.input = !this.input;
        //console.log('this.input', this.input);
        //this.emit(this.input);

        /*setInterval(() => {
            this.emit(new Date())
        }, 1000);*/
    }

    emit(data: any) {
        //this.rxEmitter.next(data);
        let data = !data;
        this.rxEmitter.next(data);
        /*this.rxEmitter.emit({
            value: false
        });*/
        //console.log('emit');
    }
}