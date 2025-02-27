import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { IonicModule } from '@ionic/angular';

import { ModalClienteSucursalPage } from './modal-cliente-sucursal.page';

describe('ModalClienteSucursalPage', () => {
  let component: ModalClienteSucursalPage;
  let fixture: ComponentFixture<ModalClienteSucursalPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ModalClienteSucursalPage],
      imports: [IonicModule.forRoot()]
    }).compileComponents();

    fixture = TestBed.createComponent(ModalClienteSucursalPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});