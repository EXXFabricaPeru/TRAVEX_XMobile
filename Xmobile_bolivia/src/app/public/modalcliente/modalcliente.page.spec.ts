import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ModalclientePage } from './modalcliente.page';

describe('ModalclientePage', () => {
  let component: ModalclientePage;
  let fixture: ComponentFixture<ModalclientePage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ModalclientePage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ModalclientePage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
