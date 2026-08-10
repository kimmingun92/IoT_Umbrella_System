# sql/

`schema.sql` — MySQL 테이블 3개(user, slot, log) 생성 스크립트.

## 적용
```bash
mysql -u root -p < schema.sql
```

## 테이블 요약
- **user**: `card_uid`(RFID UID, UNIQUE)를 키로 사용자 등록 정보 저장
- **slot**: 슬롯별 현재 상태(`EMPTY`/`USING`/`DRYING`/`DRY_DONE`/`THEFT`), 배정된 UID, 건조도 저장
- **log**: 슬롯/카드별 이벤트(`STORE`/`PICKUP`/`THEFT`/`DRY_UPDATE`/`DRY_DONE`/`AUTH_FAIL`) 이력 저장

애플리케이션에서 접속에 사용하는 계정(`iot`/`pwiot`)은 별도로 생성해서 `iotdb`에 권한을 부여해야 한다.
```sql
CREATE USER 'iot'@'localhost' IDENTIFIED BY 'pwiot';
GRANT ALL PRIVILEGES ON iotdb.* TO 'iot'@'localhost';
FLUSH PRIVILEGES;
```
