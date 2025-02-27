import { Component, OnInit } from '@angular/core';
import { Nit } from "../../models/nit";
import { ModalController, NavParams } from "@ionic/angular";
import { Toast } from "@ionic-native/toast/ngx";
import { promocionaes } from '../../models/promociones';

@Component({
  selector: 'app-modal-nit',
  templateUrl: './modal-nit.page.html',
  styleUrls: ['./modal-nit.page.scss'],
})
export class ModalNitPage implements OnInit {
  public items: any;
  public loadItem: boolean;
  public textPaso: boolean;
  public searchText: string;
  public statusline: boolean;
  public datatext: any;
  public userdata: any;
  public consolidador: string;
  public docidicaciones: string;
  modelPromo = new promocionaes();


  constructor(public modalController: ModalController, private toast: Toast, public navParams: NavParams) {
      this.textPaso = false;
      this.statusline = false;
      this.searchText = '';
      this.docidicaciones = '';
      this.datatext = navParams.data;
  }

  ngOnInit() {
    this.items = [];
    this.loadItem = false;
    this.bustartodo();
  }

  public async bustartodo(){
    this.statusline = false;
    this.items = [];
    this.loadItem = true;
    let model = new Nit();
    this.items = await model.busqueda_inicial();
    this.loadItem = false;
  }

public async buscar(event: any) {
    this.searchText = event.detail.value;
    this.statusline = false;
    this.items = [];
    this.loadItem = true;
    let search = event.detail.value;
    let model = new Nit();
    this.items = await model.findSearch(search);
    this.loadItem = false;

}

public cerrar() {
    this.modalController.dismiss(false);
}

public async findItem(item: any) {
    console.log("findItem.. () ", item)
    console.log("findItem..", this.statusline);
    this.modalController.dismiss(item);
}




}
