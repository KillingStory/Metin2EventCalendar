## Installation Steps:

1.Edit server information in "index.php"

2.Into Navicat "account" database press "Ctrl+Q" and paste:

```php
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `event_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;
```
3.Ready to use!
_______________________________________

## Useful links:
**Website:** https://mt2-services.eu

**Discord:** https://discord.gg/XudT2gt
