import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AnularPage } from './anular.page';

describe('AnularPage', () => {
  let component: AnularPage;
  let fixture: ComponentFixture<AnularPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AnularPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AnularPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
